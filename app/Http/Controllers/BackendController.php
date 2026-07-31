<?php

namespace App\Http\Controllers;

use App\Mail\StockAvailableMail;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use App\Models\SiteSetting;
use App\Models\StockNotification;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
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

    public function dashboard(Request $request): View
    {
        $range = (int) $request->query('range', 30);
        $range = in_array($range, [7, 30, 90, 365], true) ? $range : 30;
        $orderStatusFilter = trim((string) $request->query('order_status', ''));
        $categoryFilter = (int) $request->query('category', 0);
        $channelFilter = trim((string) $request->query('channel', ''));
        $rangeStart = now()->startOfDay()->subDays($range - 1);
        $applyFilters = function ($query) use ($orderStatusFilter, $categoryFilter, $channelFilter) {
            return $query
                ->when($orderStatusFilter, fn ($query) => $query->where(fn ($query) => $query
                    ->where('status', $orderStatusFilter)
                    ->orWhere('payment_status', $orderStatusFilter)
                    ->orWhere('tracking_status', $orderStatusFilter)))
                ->when($categoryFilter, fn ($query) => $query->whereHas('items.product', fn ($query) => $query->where('category_id', $categoryFilter)))
                ->when($channelFilter, fn ($query) => $query->where('payment_method', $channelFilter));
        };
        $rangeOrders = $applyFilters(Order::query()->withCount('items'))
            ->where('created_at', '>=', $rangeStart)
            ->oldest()
            ->get();
        $previousOrders = $applyFilters(Order::query())
            ->whereBetween('created_at', [$rangeStart->copy()->subDays($range), $rangeStart->copy()->subSecond()])
            ->get();
        $dailyOrders = $rangeOrders->groupBy(fn (Order $order) => $order->created_at->format('Y-m-d'));
        $chartDates = collect(range(0, $range - 1))->map(fn (int $offset) => $rangeStart->copy()->addDays($offset));
        $chartLabels = $chartDates->map(fn ($date) => $date->format('M d'));
        $revenueSeries = $chartDates->map(fn ($date) => round((float) ($dailyOrders->get($date->format('Y-m-d'))?->sum('total') ?? 0), 2));
        $ordersSeries = $chartDates->map(fn ($date) => (int) ($dailyOrders->get($date->format('Y-m-d'))?->count() ?? 0));
        $topCustomers = $rangeOrders
            ->groupBy(fn (Order $order) => strtolower($order->email))
            ->map(function ($orders) {
                $latest = $orders->last();

                return [
                    'name' => $latest->customer_name,
                    'email' => $latest->email,
                    'orders' => $orders->count(),
                    'spent' => (float) $orders->sum('total'),
                ];
            })
            ->sortByDesc('spent')
            ->take(5)
            ->values();
        $activeProducts = Product::where('status', 'active')->count();
        $inactiveProducts = Product::where('status', 'inactive')->count();
        $activeCategories = Category::where('status', 'active')->count();
        $inactiveCategories = Category::where('status', 'inactive')->count();
        $activeUsers = User::where('status', 'active')->count();
        $paidOrders = Order::where('payment_status', 'paid')->count();
        $unpaidOrders = Order::where('payment_status', 'unpaid')->count();
        $totalOrders = Order::count();
        $totalRevenue = Order::sum('total');
        $trackingStatuses = ['placed', 'processing', 'packed', 'dispatched', 'out_for_delivery', 'delivered', 'cancelled'];
        $paymentStatuses = ['unpaid', 'paid', 'failed', 'refunded'];
        $topProducts = OrderItem::query()
            ->selectRaw('product_id, product_name, product_sku, product_image, SUM(quantity) as sold_quantity, SUM(line_total) as sold_total')
            ->whereIn('order_id', $rangeOrders->pluck('id'))
            ->groupBy('product_id', 'product_name', 'product_sku', 'product_image')
            ->orderByDesc('sold_quantity')
            ->take(8)
            ->get();
        $categoryPerformance = OrderItem::with('product.category')
            ->whereIn('order_id', $rangeOrders->pluck('id'))
            ->get()
            ->groupBy(fn (OrderItem $item) => $item->product?->category?->name ?? 'Uncategorised')
            ->map(fn ($items, string $name) => [
                'name' => $name,
                'revenue' => (float) $items->sum('line_total'),
                'units' => (int) $items->sum('quantity'),
            ])
            ->sortByDesc('revenue')
            ->take(6)
            ->values();
        $channelPerformance = $rangeOrders
            ->groupBy(fn (Order $order) => $order->payment_method ?: 'Unknown')
            ->map(fn ($orders, string $name) => [
                'name' => Str::headline($name),
                'orders' => $orders->count(),
                'revenue' => (float) $orders->sum('total'),
            ])
            ->sortByDesc('revenue')
            ->values();

        $rangeRevenue = (float) $rangeOrders->sum('total');
        $previousRevenue = (float) $previousOrders->sum('total');
        $percentageChange = static fn (float $current, float $previous): ?float => $previous > 0
            ? round((($current - $previous) / $previous) * 100, 1)
            : ($current > 0 ? 100.0 : null);
        $currentItems = (int) OrderItem::whereIn('order_id', $rangeOrders->pluck('id'))->sum('quantity');
        $previousItems = (int) OrderItem::whereIn('order_id', $previousOrders->pluck('id'))->sum('quantity');
        $rangeCustomers = $rangeOrders->pluck('email')->map(fn ($email) => strtolower($email))->unique()->count();
        $previousCustomers = $previousOrders->pluck('email')->map(fn ($email) => strtolower($email))->unique()->count();
        $filteredPaidOrders = $rangeOrders->where('payment_status', 'paid');

        return view('backend.dashboard', [
            'totalProducts' => Product::count(),
            'totalCategories' => Category::count(),
            'totalUsers' => User::count(),
            'totalOrders' => $totalOrders,
            'todayOrders' => Order::whereDate('created_at', today())->count(),
            'totalRevenue' => $totalRevenue,
            'averageOrderValue' => $totalOrders > 0 ? $totalRevenue / $totalOrders : 0,
            'paidOrders' => $paidOrders,
            'unpaidOrders' => $unpaidOrders,
            'paidRevenue' => Order::where('payment_status', 'paid')->sum('total'),
            'pendingRevenue' => Order::where('payment_status', 'unpaid')->sum('total'),
            'activeProducts' => $activeProducts,
            'inactiveProducts' => $inactiveProducts,
            'activeCategories' => $activeCategories,
            'inactiveCategories' => $inactiveCategories,
            'activeUsers' => $activeUsers,
            'reviewsCount' => Review::count(),
            'orderItemsCount' => OrderItem::count(),
            'totalInventory' => Product::sum('stock'),
            'inventoryValue' => Product::selectRaw('SUM(price * stock) as value')->value('value') ?? 0,
            'topProducts' => $topProducts,
            'recentProducts' => Product::with('category')->latest()->take(5)->get(),
            'recentCategories' => Category::withCount('products')->latest()->take(5)->get(),
            'recentUsers' => User::latest()->take(5)->get(),
            'recentOrders' => $rangeOrders->sortByDesc('created_at')->take(8)->values(),
            'trackingBreakdown' => collect($trackingStatuses)->mapWithKeys(fn ($status) => [$status => Order::where('tracking_status', $status)->count()]),
            'paymentBreakdown' => collect($paymentStatuses)->mapWithKeys(fn ($status) => [$status => Order::where('payment_status', $status)->count()]),
            'lowStockProducts' => Product::with('category')->where('stock', '<=', 5)->orderBy('stock')->take(5)->get(),
            'range' => $range,
            'rangeRevenue' => $rangeRevenue,
            'revenueChange' => $percentageChange($rangeRevenue, $previousRevenue),
            'ordersChange' => $percentageChange((float) $rangeOrders->count(), (float) $previousOrders->count()),
            'itemsChange' => $percentageChange((float) $currentItems, (float) $previousItems),
            'customerChange' => $percentageChange((float) $rangeCustomers, (float) $previousCustomers),
            'rangeCustomers' => $rangeCustomers,
            'rangeAverageOrder' => $rangeOrders->count() ? $rangeRevenue / $rangeOrders->count() : 0,
            'rangePaidRevenue' => (float) $filteredPaidOrders->sum('total'),
            'rangePaidOrders' => $filteredPaidOrders->count(),
            'rangeOrders' => $rangeOrders->count(),
            'chartLabels' => $chartLabels,
            'revenueSeries' => $revenueSeries,
            'ordersSeries' => $ordersSeries,
            'topCustomers' => $topCustomers,
            'categoryInsights' => Category::withCount('products')->orderByDesc('products_count')->take(5)->get(),
            'orderStatusFilter' => $orderStatusFilter,
            'categoryFilter' => $categoryFilter,
            'channelFilter' => $channelFilter,
            'filterCategories' => Category::where('status', 'active')->orderBy('name')->get(['id', 'name']),
            'filterChannels' => Order::whereNotNull('payment_method')->distinct()->orderBy('payment_method')->pluck('payment_method'),
            'itemsSold' => $currentItems,
            'deliveredOrders' => $rangeOrders->where('tracking_status', 'delivered')->count(),
            'pendingOrders' => $rangeOrders->whereNotIn('tracking_status', ['delivered', 'cancelled'])->count(),
            'categoryPerformance' => $categoryPerformance,
            'channelPerformance' => $channelPerformance,
            'filteredTrackingBreakdown' => collect($trackingStatuses)->mapWithKeys(fn ($status) => [$status => $rangeOrders->where('tracking_status', $status)->count()]),
            'filteredPaymentBreakdown' => collect($paymentStatuses)->mapWithKeys(fn ($status) => [$status => $rangeOrders->where('payment_status', $status)->count()]),
        ]);
    }

    public function page(Request $request, string $page): View
    {
        $allowedPages = [
            'products',
            'categories',
            'users',
            'orders',
            'reviews',
            'stock-notifications',
            'settings',
            'reports',
        ];

        abort_unless(in_array($page, $allowedPages, true), 404);

        $pageTitle = ucwords(str_replace('-', ' ', $page));
        $search = trim((string) $request->query('q', ''));
        $status = trim((string) $request->query('status', ''));
        $role = trim((string) $request->query('role', ''));
        $category = (int) $request->query('category', 0);
        $records = match ($page) {
            'products' => Product::with('category')
                ->when($search, fn ($query) => $query->where(fn ($query) => $query
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhereHas('category', fn ($query) => $query->where('name', 'like', "%{$search}%"))))
                ->when($category, fn ($query) => $query->where('category_id', $category))
                ->when($status, fn ($query) => $query->where('status', $status))
                ->latest()
                ->paginate(10)
                ->withQueryString(),
            'categories' => Category::withCount('products')
                ->when($search, fn ($query) => $query->where(fn ($query) => $query
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")))
                ->when($status, fn ($query) => $query->where('status', $status))
                ->latest()
                ->paginate(10)
                ->withQueryString(),
            'users' => User::query()
                ->when($search, fn ($query) => $query->where(fn ($query) => $query
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")))
                ->when($role, fn ($query) => $query->where('role', $role))
                ->when($status, fn ($query) => $query->where('status', $status))
                ->latest()
                ->paginate(10)
                ->withQueryString(),
            'reviews' => Review::with('product')
                ->when($search, fn ($query) => $query->where(fn ($query) => $query
                    ->where('customer_name', 'like', "%{$search}%")
                    ->orWhere('comment', 'like', "%{$search}%")
                    ->orWhereHas('product', fn ($query) => $query->where('name', 'like', "%{$search}%"))))
                ->when($status, fn ($query) => $query->where('status', $status))
                ->latest()
                ->paginate(10)
                ->withQueryString(),
            'stock-notifications' => StockNotification::with('product')
                ->when($search, fn ($query) => $query->where(fn ($query) => $query
                    ->where('customer_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhereHas('product', fn ($query) => $query->where('name', 'like', "%{$search}%")->orWhere('sku', 'like', "%{$search}%"))))
                ->when($status, fn ($query) => $query->where('status', $status))
                ->latest()
                ->paginate(10)
                ->withQueryString(),
            'orders' => Order::with('items')
                ->withCount('items')
                ->when($search, fn ($query) => $query->where(fn ($query) => $query
                    ->where('order_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('tracking_number', 'like', "%{$search}%")))
                ->when($status, fn ($query) => $query->where(fn ($query) => $query
                    ->where('status', $status)
                    ->orWhere('payment_status', $status)
                    ->orWhere('tracking_status', $status)))
                ->latest()
                ->paginate(10)
                ->withQueryString(),
            default => null,
        };

        $reportStats = $page === 'reports' ? [
            'orders' => Order::count(),
            'paid_count' => Order::where('payment_status', 'paid')->count(),
            'paid_total' => Order::where('payment_status', 'paid')->sum('total'),
            'unpaid_count' => Order::where('payment_status', 'unpaid')->count(),
            'unpaid_total' => Order::where('payment_status', 'unpaid')->sum('total'),
            'cancelled_count' => Order::where('status', 'cancelled')->count(),
            'recent' => Order::with('items')->latest()->take(10)->get(),
        ] : null;
        $userStats = $page === 'users' ? [
            'total' => User::count(),
            'active' => User::where('status', 'active')->count(),
            'inactive' => User::where('status', 'inactive')->count(),
            'pending' => User::whereNotIn('status', ['active', 'inactive'])->count(),
            'paid' => Order::query()->distinct('email')->count('email'),
            'roles' => User::query()->whereNotNull('role')->distinct()->orderBy('role')->pluck('role'),
        ] : null;
        $orderStats = $page === 'orders' ? [
            'total' => Order::count(),
            'revenue' => (float) Order::sum('total'),
            'paid' => Order::where('payment_status', 'paid')->count(),
            'paidRevenue' => (float) Order::where('payment_status', 'paid')->sum('total'),
            'unpaid' => Order::where('payment_status', 'unpaid')->count(),
            'pending' => Order::whereNotIn('tracking_status', ['delivered', 'cancelled'])->count(),
            'delivered' => Order::where('tracking_status', 'delivered')->count(),
            'proofs' => Order::whereNotNull('payment_proof')->where('payment_proof', '!=', '')->count(),
        ] : null;

        if ($page === 'settings') {
            return view('backend.pages.settings', [
                'page' => $page,
                'pageTitle' => 'Site Settings',
                'settings' => $this->siteSettings(),
            ]);
        }

        return view('backend.pages.placeholder', [
            'page' => $page,
            'pageTitle' => $pageTitle,
            'records' => $records,
            'search' => $search,
            'status' => $status,
            'role' => $role,
            'categoryFilter' => $category,
            'canManageUsers' => $this->isSuperAdmin(),
            'reportStats' => $reportStats,
            'userStats' => $userStats,
            'orderStats' => $orderStats,
            'categories' => Category::where('status', 'active')->orderBy('name')->get(),
            'products' => Product::orderBy('name')->get(),
        ]);
    }

    public function notifyStockCustomer(StockNotification $stockNotification): RedirectResponse
    {
        $stockNotification->load('product');

        abort_unless($stockNotification->product, 404);

        Mail::to($stockNotification->email)->send(new StockAvailableMail($stockNotification));

        $stockNotification->update([
            'status' => 'notified',
            'notified_at' => now(),
        ]);

        return back()->with('success', 'Customer notified successfully.');
    }

    public function profile(): View
    {
        return view('backend.profile', [
            'admin' => auth()->user(),
            'editMode' => false,
            'pageTitle' => 'My Profile',
        ]);
    }

    public function orderNotifications(): JsonResponse
    {
        $orders = Order::latest()->take(50)->get()->map(fn (Order $order) => [
            'order_number' => $order->order_number,
            'customer_name' => $order->customer_name,
            'total' => number_format((float) $order->total, 2),
            'payment_status' => $order->payment_status ?? 'unpaid',
            'tracking_status' => $order->tracking_status ?? 'placed',
            'created' => $order->created_at?->diffForHumans(),
            'url' => route('backend.orders.show', $order),
        ]);

        return response()->json([
            'count' => Order::count(),
            'orders' => $orders,
        ]);
    }

    public function updateSiteSettings(Request $request): RedirectResponse
    {
        $setting = $request->validate([
            'setting' => ['required', Rule::in(['header_logo', 'footer_logo', 'company_whatsapp_number', 'admin_order_emails', 'asset_versions'])],
        ])['setting'];

        if (in_array($setting, ['header_logo', 'footer_logo'], true)) {
            $fileKey = $setting . '_file';
            $request->validate([
                $fileKey => ['required', 'file', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
            ]);

            $settings = $this->siteSettings();
            $this->deleteUploadedImage($settings[$setting] ?? null);
            $file = $request->file($fileKey);
            $filename = 'site-' . $setting . '-' . Str::uuid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('backend/assets/imgs/uploads'), $filename);
            SiteSetting::setValue($setting, 'backend/assets/imgs/uploads/' . $filename);
        }

        if ($setting === 'company_whatsapp_number') {
            $data = $request->validate([
                'company_whatsapp_number' => ['nullable', 'string', 'max:40', 'regex:/^[0-9+\s().-]+$/'],
            ]);
            SiteSetting::setValue($setting, trim((string) ($data[$setting] ?? '')));
        }

        if ($setting === 'admin_order_emails') {
            $data = $request->validate([
                'admin_order_emails' => [
                    'nullable',
                    'string',
                    'max:2000',
                    function (string $attribute, mixed $value, \Closure $fail): void {
                        foreach (array_filter(array_map('trim', explode(',', (string) $value))) as $email) {
                            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                $fail("The email address {$email} is invalid.");
                            }
                        }
                    },
                ],
            ]);
            SiteSetting::setValue(
                $setting,
                collect(explode(',', (string) ($data[$setting] ?? '')))
                    ->map(fn (string $email) => strtolower(trim($email)))
                    ->filter()
                    ->unique()
                    ->implode(',')
            );
        }

        if ($setting === 'asset_versions') {
            $data = $request->validate([
                'css_version' => ['required', 'string', 'max:50', 'regex:/^[A-Za-z0-9._-]+$/'],
                'js_version' => ['required', 'string', 'max:50', 'regex:/^[A-Za-z0-9._-]+$/'],
            ], [
                'css_version.regex' => 'The CSS version may only contain letters, numbers, dots, dashes and underscores.',
                'js_version.regex' => 'The JS version may only contain letters, numbers, dots, dashes and underscores.',
            ]);

            SiteSetting::setValue('css_version', $data['css_version']);
            SiteSetting::setValue('js_version', $data['js_version']);
        }

        return back()->with('success', Str::headline($setting) . ' updated successfully.');
    }

    public function showOrder(Order $order): View
    {
        if (! $order->tracking_number) {
            $order->forceFill(['tracking_number' => $this->royalMailTrackingNumber($order)])->save();
        }

        return view('backend.pages.order-show', [
            'order' => $order->load('items'),
            'pageTitle' => 'Order #' . $order->order_number,
        ]);
    }

    public function royalMailLabel(Order $order): View
    {
        if (! $order->tracking_number) {
            $order->forceFill(['tracking_number' => $this->royalMailTrackingNumber($order)])->save();
        }

        return view('backend.pages.royal-mail-label', [
            'order' => $order->load('items'),
            'pageTitle' => 'Royal Mail Label #' . $order->order_number,
        ]);
    }

    public function updateOrder(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'customer_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
            'address' => ['nullable', 'string', 'max:255'],
            'address_2' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:120'],
            'state' => ['nullable', 'string', 'max:120'],
            'zip' => ['nullable', 'string', 'max:20'],
            'status' => ['required', Rule::in(['pending', 'processing', 'shipped', 'delivered', 'cancelled'])],
            'payment_status' => ['required', Rule::in(['unpaid', 'paid', 'failed', 'refunded'])],
            'tracking_status' => ['required', Rule::in(['placed', 'processing', 'packed', 'dispatched', 'out_for_delivery', 'delivered', 'cancelled'])],
            'tracking_number' => ['nullable', 'string', 'max:120'],
            'tracking_note' => ['nullable', 'string', 'max:1000'],
            'admin_note' => ['nullable', 'string', 'max:1000'],
        ]);

        if (empty($data['tracking_number'])) {
            $data['tracking_number'] = $this->royalMailTrackingNumber($order);
        }

        if (! empty($data['zip'])) {
            $data['zip'] = strtoupper(preg_replace('/\s+/', ' ', trim($data['zip'])));
        }

        $order->update($data);

        return back()->with('success', 'Order updated successfully.');
    }

    public function updateOrderPaymentProof(Request $request, Order $order): RedirectResponse
    {
        $request->validate([
            'payment_proof_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        if (! $request->hasFile('payment_proof_file') && ! $order->payment_proof) {
            return back()
                ->withErrors(['payment_proof_file' => 'Please upload payment proof before marking this order paid.'])
                ->withInput();
        }

        if ($request->hasFile('payment_proof_file')) {
            $this->deleteUploadedImage($order->payment_proof);

            $file = $request->file('payment_proof_file');
            $filename = 'payment-proof-' . $order->id . '-' . Str::uuid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('backend/assets/imgs/uploads'), $filename);

            $paymentData['payment_proof'] = 'backend/assets/imgs/uploads/' . $filename;
            $paymentData['payment_proof_submitted_at'] = now();
        } elseif (! $order->payment_proof_submitted_at) {
            $paymentData['payment_proof_submitted_at'] = now();
        }

        $order->update([
            ...($paymentData ?? []),
            'payment_status' => 'paid',
        ]);

        return back()->with('success', 'Payment proof saved and order marked paid.');
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
            'products' => Product::orderBy('name')->get(),
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
            'reviews' => Review::create($this->reviewData($request)),
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
            'products' => Product::orderBy('name')->get(),
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
            'reviews' => $record->update($this->reviewData($request)),
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

    public function toggleStatus(Request $request, string $resource, int $id): RedirectResponse|JsonResponse
    {
        $this->authorizeResource($resource);
        $record = $this->findRecord($resource, $id);

        abort_if($resource === 'users' && auth()->id() === $record->id, 403);

        $newStatus = $record->status === 'active' ? 'inactive' : 'active';
        $record->update(['status' => $newStatus]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'active' => $newStatus === 'active',
                'label' => Str::headline($newStatus),
                'message' => 'Status updated successfully.',
            ]);
        }

        return back()->with('success', 'Status updated successfully.');
    }

    public function toggleProductField(Request $request, Product $product, string $field): RedirectResponse|JsonResponse
    {
        abort_unless(in_array($field, ['stock', 'is_bestseller'], true), 404);

        $newValue = $field === 'stock'
            ? ($product->stock > 0 ? 0 : 1)
            : ! $product->is_bestseller;

        $product->update([$field => $newValue]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'active' => (bool) $newValue,
                'label' => $newValue ? 'Active' : 'Inactive',
                'message' => Str::headline($field) . ' updated successfully.',
            ]);
        }

        return back()->with('success', Str::headline($field) . ' updated successfully.');
    }

    private function authorizeResource(string $resource): void
    {
        abort_unless(in_array($resource, ['products', 'categories', 'users', 'reviews'], true), 404);
        abort_if($resource === 'users' && ! $this->isSuperAdmin(), 403);
    }

    private function findRecord(string $resource, int $id): Product|Category|User|Review
    {
        return match ($resource) {
            'products' => Product::findOrFail($id),
            'categories' => Category::findOrFail($id),
            'users' => User::findOrFail($id),
            'reviews' => Review::findOrFail($id),
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
            'stock' => ['required', Rule::in(['0', '1', 0, 1])],
            'reviews_count' => ['required', 'integer', 'min:0'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'is_featured' => ['nullable', 'boolean'],
            'is_bestseller' => ['nullable', 'boolean'],
        ]);

        $data['slug'] = $this->uniqueSlug(Product::class, $data['slug'] ?: Str::slug($data['name']), $ignoreId);
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_bestseller'] = $request->boolean('is_bestseller');
        $data['stock'] = (int) $data['stock'];
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

    private function reviewData(Request $request): array
    {
        $data = $request->validate([
            'product_id' => ['nullable', 'exists:products,id'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_title' => ['nullable', 'string', 'max:255'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string'],
            'avatar' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'is_featured' => ['nullable', 'boolean'],
        ]);

        $data['is_featured'] = $request->boolean('is_featured');

        return $data;
    }

    private function royalMailTrackingNumber(Order $order): string
    {
        $seed = strtoupper(substr(preg_replace('/[^A-Z0-9]/', '', $order->order_number), -6));
        $checksum = str_pad((string) (($order->id * 37 + now()->dayOfYear) % 1000), 3, '0', STR_PAD_LEFT);

        return 'RM' . $seed . $checksum . 'GB';
    }

    private function siteSettings(): array
    {
        return array_merge([
            'header_logo' => 'frontend/assets/images/logo/logo-transperent.png',
            'footer_logo' => 'frontend/assets/images/logo/logo.png',
            'company_whatsapp_number' => '',
            'admin_order_emails' => '',
            'css_version' => '1.0.0',
            'js_version' => '1.0.0',
        ], SiteSetting::allKeyed());
    }
}
