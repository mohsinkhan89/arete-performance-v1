<?php

namespace App\Http\Controllers;

use App\Mail\PaymentProofStatusMail;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BackendController extends Controller
{
    private function isSuperAdmin(): bool
    {
        return str_contains(strtolower(auth()->user()->role ?? ''), 'super');
    }

    public function dashboard(): View
    {
        $activeProducts = Product::where('status', 'active')->count();
        $inactiveProducts = Product::where('status', 'inactive')->count();
        $activeCategories = Category::where('status', 'active')->count();
        $inactiveCategories = Category::where('status', 'inactive')->count();
        $activeUsers = User::where('status', 'active')->count();
        $paidOrders = Order::where('payment_status', 'paid')->count();
        $unpaidOrders = Order::whereIn('payment_status', ['unpaid', 'proof_submitted'])->count();
        $proofSubmittedOrders = Order::where('payment_status', 'proof_submitted')->count();

        return view('backend.dashboard', [
            'totalProducts' => Product::count(),
            'totalCategories' => Category::count(),
            'totalUsers' => User::count(),
            'totalOrders' => Order::count(),
            'paidOrders' => $paidOrders,
            'unpaidOrders' => $unpaidOrders,
            'proofSubmittedOrders' => $proofSubmittedOrders,
            'paidRevenue' => Order::where('payment_status', 'paid')->sum('total'),
            'pendingRevenue' => Order::whereIn('payment_status', ['unpaid', 'proof_submitted'])->sum('total'),
            'proofSubmittedTotal' => Order::where('payment_status', 'proof_submitted')->sum('total'),
            'activeProducts' => $activeProducts,
            'inactiveProducts' => $inactiveProducts,
            'activeCategories' => $activeCategories,
            'inactiveCategories' => $inactiveCategories,
            'activeUsers' => $activeUsers,
            'totalInventory' => Product::sum('stock'),
            'inventoryValue' => Product::selectRaw('SUM(price * stock) as value')->value('value') ?? 0,
            'topProducts' => Product::with('category')->latest()->take(5)->get(),
            'recentProducts' => Product::with('category')->latest()->take(5)->get(),
            'recentCategories' => Category::withCount('products')->latest()->take(5)->get(),
            'recentUsers' => User::latest()->take(5)->get(),
            'recentOrders' => Order::latest()->take(5)->get(),
            'recentPaymentProofs' => Order::where('payment_status', 'proof_submitted')->latest('payment_proof_submitted_at')->take(5)->get(),
        ]);
    }

    public function page(string $page): View
    {
        $allowedPages = [
            'products',
            'categories',
            'users',
            'orders',
            'customers',
            'inventory',
            'coupons',
            'reviews',
            'pages',
            'banners',
            'settings',
            'reports',
        ];

        abort_unless(in_array($page, $allowedPages, true), 404);

        $pageTitle = ucwords(str_replace('-', ' ', $page));
        $records = match ($page) {
            'products' => Product::with('category')->latest()->paginate(10),
            'categories' => Category::withCount('products')->latest()->paginate(10),
            'users' => User::latest()->paginate(10),
            'reviews' => Review::with('product')->latest()->paginate(10),
            'orders' => Order::with('items')->withCount('items')->latest()->paginate(10),
            default => null,
        };

        $reportStats = $page === 'reports' ? [
            'orders' => Order::count(),
            'paid_count' => Order::where('payment_status', 'paid')->count(),
            'paid_total' => Order::where('payment_status', 'paid')->sum('total'),
            'proof_count' => Order::where('payment_status', 'proof_submitted')->count(),
            'proof_total' => Order::where('payment_status', 'proof_submitted')->sum('total'),
            'unpaid_count' => Order::where('payment_status', 'unpaid')->count(),
            'unpaid_total' => Order::where('payment_status', 'unpaid')->sum('total'),
            'cancelled_count' => Order::where('status', 'cancelled')->count(),
            'recent' => Order::latest()->take(10)->get(),
        ] : null;

        return view('backend.pages.placeholder', [
            'page' => $page,
            'pageTitle' => $pageTitle,
            'records' => $records,
            'canManageUsers' => $this->isSuperAdmin(),
            'reportStats' => $reportStats,
        ]);
    }

    public function profile(): View
    {
        return view('backend.profile', [
            'admin' => auth()->user(),
            'editMode' => false,
            'pageTitle' => 'My Profile',
        ]);
    }

    public function showOrder(Order $order): View
    {
        return view('backend.pages.order-show', [
            'order' => $order->load('items'),
            'pageTitle' => 'Order #' . $order->order_number,
        ]);
    }

    public function updateOrder(Request $request, Order $order): RedirectResponse
    {
        $previousPaymentStatus = $order->payment_status;

        $data = $request->validate([
            'status' => ['required', Rule::in(['pending', 'processing', 'shipped', 'delivered', 'cancelled'])],
            'payment_status' => ['required', Rule::in(['unpaid', 'proof_submitted', 'paid', 'failed', 'refunded'])],
            'tracking_status' => ['required', Rule::in(['placed', 'processing', 'packed', 'dispatched', 'out_for_delivery', 'delivered', 'cancelled'])],
            'tracking_number' => ['nullable', 'string', 'max:120'],
            'tracking_note' => ['nullable', 'string', 'max:1000'],
            'admin_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $order->update($data);

        if ($previousPaymentStatus !== $order->payment_status && in_array($order->payment_status, ['paid', 'failed'], true)) {
            Mail::to($order->email)->send(new PaymentProofStatusMail($order->fresh(), $order->payment_status === 'paid' ? 'accepted' : 'rejected'));
        }

        return back()->with('success', 'Order updated successfully.');
    }

    public function editProfile(): View
    {
        return view('backend.profile', [
            'admin' => auth()->user(),
            'editMode' => true,
            'pageTitle' => 'Edit Profile',
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $admin = auth()->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($admin->id)],
            'phone' => ['nullable', 'string', 'max:40'],
            'current_password' => ['nullable', 'required_with:password', 'current_password'],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ]);

        unset($data['current_password'], $data['password_confirmation']);

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $admin->update($data);

        return redirect()->route('backend.profile')->with('success', 'Profile updated successfully.');
    }

    public function create(string $resource): View
    {
        $this->authorizeResource($resource);

        return view('backend.pages.form', [
            'resource' => $resource,
            'record' => null,
            'categories' => Category::where('status', 'active')->orderBy('name')->get(),
            'pageTitle' => 'Add ' . Str::headline(Str::singular($resource)),
        ]);
    }

    public function store(Request $request, string $resource): RedirectResponse
    {
        $this->authorizeResource($resource);

        match ($resource) {
            'products' => Product::create($this->productData($request)),
            'categories' => Category::create($this->categoryData($request)),
            'users' => User::create($this->userData($request)),
            default => abort(404),
        };

        return redirect()->route('backend.page', $resource)->with('success', Str::headline(Str::singular($resource)) . ' added successfully.');
    }

    public function edit(string $resource, int $id): View
    {
        $this->authorizeResource($resource);

        return view('backend.pages.form', [
            'resource' => $resource,
            'record' => $this->findRecord($resource, $id),
            'categories' => Category::where('status', 'active')->orderBy('name')->get(),
            'pageTitle' => 'Edit ' . Str::headline(Str::singular($resource)),
        ]);
    }

    public function update(Request $request, string $resource, int $id): RedirectResponse
    {
        $this->authorizeResource($resource);

        $record = $this->findRecord($resource, $id);

        match ($resource) {
            'products' => $record->update($this->productData($request, $record->id, $record->image, $record->test_report_image)),
            'categories' => $record->update($this->categoryData($request, $record->id, $record->image)),
            'users' => $record->update($this->userData($request, $record->id)),
            default => abort(404),
        };

        return redirect()->route('backend.page', $resource)->with('success', Str::headline(Str::singular($resource)) . ' updated successfully.');
    }

    public function destroy(string $resource, int $id): RedirectResponse
    {
        $this->authorizeResource($resource);
        $record = $this->findRecord($resource, $id);

        abort_if($resource === 'users' && auth()->id() === $record->id, 403);

        if (in_array($resource, ['products', 'categories'], true)) {
            $this->deleteUploadedImage($record->image);
        }

        if ($resource === 'products') {
            $this->deleteUploadedImage($record->test_report_image);
        }

        $record->delete();

        return back()->with('success', Str::headline(Str::singular($resource)) . ' deleted successfully.');
    }

    public function toggleStatus(string $resource, int $id): RedirectResponse
    {
        $this->authorizeResource($resource);
        $record = $this->findRecord($resource, $id);

        abort_if($resource === 'users' && auth()->id() === $record->id, 403);

        $record->update([
            'status' => $record->status === 'active' ? 'inactive' : 'active',
        ]);

        return back()->with('success', 'Status updated successfully.');
    }

    private function authorizeResource(string $resource): void
    {
        abort_unless(in_array($resource, ['products', 'categories', 'users'], true), 404);
        abort_if($resource === 'users' && ! $this->isSuperAdmin(), 403);
    }

    private function findRecord(string $resource, int $id): Product|Category|User
    {
        return match ($resource) {
            'products' => Product::findOrFail($id),
            'categories' => Category::findOrFail($id),
            'users' => User::findOrFail($id),
            default => abort(404),
        };
    }

    private function productData(Request $request, ?int $ignoreId = null, ?string $currentImage = null, ?string $currentTestReportImage = null): array
    {
        $data = $request->validate([
            'category_id' => ['nullable', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('products', 'slug')->ignore($ignoreId)],
            'sku' => ['required', 'string', 'max:255', Rule::unique('products', 'sku')->ignore($ignoreId)],
            'short_description' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_image' => ['nullable', 'boolean'],
            'test_report_image_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_test_report_image' => ['nullable', 'boolean'],
            'price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'is_featured' => ['nullable', 'boolean'],
        ]);

        $data['slug'] = $this->uniqueSlug(Product::class, $data['slug'] ?: Str::slug($data['name']), $ignoreId);
        $data['is_featured'] = $request->boolean('is_featured');
        $data['image'] = $this->imageValue($request, 'image_file', 'remove_image', $currentImage);
        $data['test_report_image'] = $this->imageValue($request, 'test_report_image_file', 'remove_test_report_image', $currentTestReportImage);

        unset($data['image_file'], $data['remove_image'], $data['test_report_image_file'], $data['remove_test_report_image']);

        return $data;
    }

    private function categoryData(Request $request, ?int $ignoreId = null, ?string $currentImage = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('categories', 'slug')->ignore($ignoreId)],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'string', 'max:255'],
            'image_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_image' => ['nullable', 'boolean'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'sort_order' => ['required', 'integer', 'min:0'],
        ]);

        $data['slug'] = $this->uniqueSlug(Category::class, $data['slug'] ?: Str::slug($data['name']), $ignoreId);
        $data['image'] = $this->imageValue($request, 'image_file', 'remove_image', $currentImage, $data['image'] ?? null);

        unset($data['image_file'], $data['remove_image']);

        return $data;
    }

    private function imageValue(Request $request, string $fileKey, string $removeKey, ?string $currentImage = null, ?string $pathValue = null): ?string
    {
        if ($request->hasFile($fileKey)) {
            $this->deleteUploadedImage($currentImage);

            $file = $request->file($fileKey);
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('backend/assets/imgs/uploads'), $filename);

            return 'backend/assets/imgs/uploads/' . $filename;
        }

        if ($request->boolean($removeKey)) {
            $this->deleteUploadedImage($currentImage);

            return null;
        }

        return $pathValue ?: $currentImage;
    }

    private function deleteUploadedImage(?string $image): void
    {
        if (! $image || ! str_starts_with($image, 'backend/assets/imgs/uploads/')) {
            return;
        }

        $path = public_path($image);

        if (File::exists($path)) {
            File::delete($path);
        }
    }

    private function uniqueSlug(string $modelClass, string $slug, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($slug);
        $nextSlug = $baseSlug;
        $index = 2;

        while ($modelClass::where('slug', $nextSlug)->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))->exists()) {
            $nextSlug = $baseSlug . '-' . $index;
            $index++;
        }

        return $nextSlug;
    }

    private function userData(Request $request, ?int $ignoreId = null): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($ignoreId)],
            'phone' => ['nullable', 'string', 'max:40'],
            'role' => ['required', Rule::in(['superadmin', 'admin'])],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'password' => [$ignoreId ? 'nullable' : 'required', 'string', 'min:6'],
        ];

        $data = $request->validate($rules);

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        return $data;
    }
}
