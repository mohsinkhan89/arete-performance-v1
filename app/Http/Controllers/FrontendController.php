<?php

namespace App\Http\Controllers;

use App\Mail\OrderPlacedMail;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\StockNotification;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class FrontendController extends Controller
{
    public function index()
    {
        return view('frontend.index', [
            'categories' => Category::withCount('products')->where('status', 'active')->orderBy('sort_order')->take(8)->get(),
            'featuredProducts' => Product::with('category')
                ->where('status', 'active')
                ->where('is_bestseller', true)
                ->orderByRaw('stock > 0 desc')
                ->latest()
                ->get(),
            'reviews' => Review::where('status', 'active')->latest()->take(5)->get(),
        ]);
    }

    public function myCart(CartService $cart)
    {
        return view('frontend.my-cart', [
            'cart' => $cart->summary(),
        ]);
    }

    public function checkout(CartService $cart)
    {
        return view('frontend.checkout', [
            'cart' => $cart->summary(),
        ]);
    }

    public function lookupPostcode(Request $request): JsonResponse
    {
        $data = $request->validate([
            'postcode' => ['required', 'string', 'max:10', 'regex:/^([A-Z]{1,2}\d[A-Z\d]?\s?\d[A-Z]{2})$/i'],
        ]);

        $postcode = strtoupper(preg_replace('/\s+/', ' ', trim($data['postcode'])));
        $postcodeUrl = rawurlencode($postcode);
        $addressApiKey = env('GETADDRESS_API_KEY');

        if ($addressApiKey) {
            try {
                $response = Http::timeout(8)->get("https://api.getAddress.io/find/{$postcodeUrl}", [
                    'api-key' => $addressApiKey,
                    'expand' => 'true',
                ]);

                if ($response->successful()) {
                    $payload = $response->json();
                    $addresses = collect($payload['addresses'] ?? [])->map(function ($address) {
                        if (is_array($address)) {
                            $line1 = trim(collect([$address['line_1'] ?? null, $address['line_2'] ?? null])->filter()->implode(', '));
                            $label = collect([
                                $line1,
                                $address['town_or_city'] ?? null,
                                $address['county'] ?? null,
                            ])->filter()->implode(', ');

                            return [
                                'label' => $label,
                                'address' => $line1 ?: $label,
                                'address_2' => trim((string) ($address['line_3'] ?? '')),
                                'city' => $address['town_or_city'] ?? '',
                                'state' => $address['county'] ?? '',
                            ];
                        }

                        $parts = collect(explode(',', (string) $address))->map(fn ($part) => trim($part))->filter()->values();

                        return [
                            'label' => $parts->implode(', '),
                            'address' => $parts->take(2)->implode(', '),
                            'address_2' => '',
                            'city' => $parts->get(max(0, $parts->count() - 2), ''),
                            'state' => $parts->last() ?? '',
                        ];
                    })->filter(fn ($address) => $address['label'] !== '')->values();

                    if ($addresses->isNotEmpty()) {
                        return response()->json([
                            'found' => true,
                            'source' => 'addresses',
                            'postcode' => $postcode,
                            'addresses' => $addresses,
                        ]);
                    }
                }
            } catch (\Throwable $exception) {
                report($exception);
            }
        }

        try {
            $response = Http::timeout(8)->get("https://api.postcodes.io/postcodes/{$postcodeUrl}");
        } catch (\Throwable $exception) {
            report($exception);

            return response()->json([
                'found' => false,
                'message' => 'Post code lookup is unavailable right now.',
            ], 503);
        }

        if (! $response->successful() || (int) $response->json('status') !== 200) {
            return response()->json([
                'found' => false,
                'message' => 'Post code not found.',
            ], 404);
        }

        $result = $response->json('result', []);
        $city = $result['admin_district'] ?? $result['parish'] ?? '';
        $county = $result['admin_county'] ?? $result['region'] ?? '';

        return response()->json([
            'found' => true,
            'source' => 'postcode',
            'postcode' => $postcode,
            'addresses' => [[
                'label' => trim("{$postcode} - enter house number and street"),
                'address' => "Post code lookup: {$postcode}",
                'address_2' => '',
                'city' => $city,
                'state' => $county,
            ]],
        ]);
    }

    public function orderSuccess()
    {
        $orderId = session('latest_order_id');
        $order = $orderId ? Order::with('items')->find($orderId) : null;

        return view('frontend.order-success', [
            'order' => $order,
        ]);
    }

    public function trackOrder(Request $request): View
    {
        $order = null;

        if ($request->filled(['order_number', 'email'])) {
            $order = Order::with('items')
                ->where('order_number', trim((string) $request->query('order_number')))
                ->where('email', trim((string) $request->query('email')))
                ->first();
        }

        return view('frontend.track-order', [
            'order' => $order,
            'searched' => $request->filled(['order_number', 'email']),
        ]);
    }

    public function productDetails(?string $product = null)
    {
        $productQuery = Product::with('category')->where('status', 'active');

        $product = $product
            ? (clone $productQuery)->where(fn ($query) => $query->where('slug', $product)->orWhere('id', $product))->firstOrFail()
            : (clone $productQuery)->latest()->firstOrFail();

        return view('frontend.product-details', [
            'product' => $product,
            'relatedProducts' => Product::with('category')
                ->where('status', 'active')
                ->where('id', '!=', $product->id)
                ->when($product->category_id, fn ($query) => $query->where('category_id', $product->category_id))
                ->orderByRaw('stock > 0 desc')
                ->latest()
                ->take(5)
                ->get(),
            'reviews' => Review::where('status', 'active')
                ->where(fn ($query) => $query->where('product_id', $product->id)->orWhereNull('product_id'))
                ->latest()
                ->take(3)
                ->get(),
        ]);
    }

    public function search(Request $request)
    {
        $filters = $this->filters($request);
        $products = $this->filteredProducts($filters)->paginate(12)->withQueryString();

        return view('frontend.search', [
            'categories' => Category::withCount(['products' => fn ($query) => $query->where('status', 'active')])->where('status', 'active')->orderBy('sort_order')->get(),
            'products' => $products,
            'filters' => $filters,
        ]);
    }

    public function searchProducts(Request $request): JsonResponse
    {
        $filters = [
            ...$this->filters($request),
            'sort' => 'name',
        ];

        $products = $this->filteredProducts($filters)
            ->take(10)
            ->get()
            ->map(fn (Product $product) => [
                'id' => $product->id,
                'name' => $product->name,
                'meta' => $product->category?->name ?? 'Product',
                'price' => (float) ($product->sale_price ?: $product->price),
                'stock' => $product->stock,
                'reviews_count' => $product->reviews_count,
                'in_stock' => $product->stock > 0,
                'image' => url($product->image ?: 'frontend/assets/images/product-bottle.png'),
            ])
            ->values();

        return response()->json([
            'products' => $products,
        ]);
    }

    public function shop(Request $request)
    {
        $filters = $this->filters($request);
        $products = $this->filteredProducts($filters)->paginate(12)->withQueryString();
        $totalProducts = Product::where('status', 'active')->count();
        $priceBounds = Product::where('status', 'active')
            ->selectRaw('FLOOR(MIN(COALESCE(sale_price, price))) as min_price, CEIL(MAX(COALESCE(sale_price, price))) as max_price')
            ->first();

        return view('frontend.shop', [
            'categories' => Category::withCount(['products' => fn ($query) => $query->where('status', 'active')])->where('status', 'active')->orderBy('sort_order')->get(),
            'products' => $products,
            'filters' => $filters,
            'totalProducts' => $totalProducts,
            'priceBounds' => [
                'min' => (int) ($priceBounds->min_price ?? 0),
                'max' => max(1, (int) ($priceBounds->max_price ?? 400)),
            ],
        ]);
    }

    public function cartJson(CartService $cart): JsonResponse
    {
        return response()->json($this->cartPayload($cart->summary()));
    }

    public function addCart(Request $request, string $product, CartService $cart): JsonResponse
    {
        $product = $this->cartProduct($product);
        abort_unless($product->status === 'active', 404);
        abort_if($product->stock <= 0, 409, 'This product is currently out of stock.');

        return response()->json($this->cartPayload($cart->add($product, (int) $request->input('quantity', 1))));
    }

    public function notifyStock(Request $request, string $product): JsonResponse
    {
        $product = Product::where('status', 'active')
            ->where(fn ($query) => $query->where('id', $product)->orWhere('slug', $product))
            ->firstOrFail();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:999'],
            'message' => ['nullable', 'string', 'max:1000'],
        ]);

        $stockNotification = StockNotification::updateOrCreate(
            [
                'product_id' => $product->id,
                'email' => strtolower($data['email']),
            ],
            [
                'customer_name' => $data['name'],
                'phone' => $data['phone'] ?? null,
                'quantity' => $data['quantity'] ?? 1,
                'message' => $data['message'] ?? null,
                'status' => 'pending',
                'notified_at' => null,
            ]
        );

        $adminEmail = config('mail.from.address');
        if ($adminEmail) {
            Mail::raw(
                "Stock notification request\n\nProduct: {$product->name}\nSKU: {$product->sku}\nRequested quantity: {$stockNotification->quantity}\n\nCustomer: {$stockNotification->customer_name}\nEmail: {$stockNotification->email}\nPhone: " . ($stockNotification->phone ?? 'N/A') . "\n\nMessage:\n" . ($stockNotification->message ?? 'N/A'),
                fn ($message) => $message
                    ->to($adminEmail)
                    ->replyTo($stockNotification->email, $stockNotification->customer_name)
                    ->subject("Stock request: {$product->name}")
            );
        }

        return response()->json([
            'message' => 'Thanks. You will be notified when this product is available.',
            'status' => 'notified',
        ]);
    }

    public function updateCart(Request $request, string $product, CartService $cart): JsonResponse
    {
        $product = $this->cartProduct($product);

        return response()->json($this->cartPayload($cart->update($product, (int) $request->input('quantity', 1))));
    }

    public function removeCart(string $product, CartService $cart): JsonResponse
    {
        $product = $this->cartProduct($product);

        return response()->json($this->cartPayload($cart->remove($product)));
    }

    public function clearCart(CartService $cart): JsonResponse
    {
        return response()->json($this->cartPayload($cart->clear()));
    }

    public function placeOrder(Request $request, CartService $cart): RedirectResponse
    {
        $summary = $cart->summary();

        if ($summary['is_empty']) {
            return redirect()->route('frontend.my-cart')->with('error', 'Your cart is empty.');
        }

        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:120'],
            'last_name' => ['required', 'string', 'max:120'],
            'company' => ['nullable', 'string', 'max:160'],
            'email' => ['required', 'email', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'address_2' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:120'],
            'state' => ['nullable', 'string', 'max:120'],
            'zip' => ['required', 'string', 'max:20'],
            'country' => ['required', 'in:United Kingdom'],
            'phone' => ['required', 'string', 'max:30'],
            'shipping_method' => ['required', 'in:uk_tracked'],
            'payment_method' => ['required', 'in:card,paypal,other'],
            'order_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $data['customer_name'] = trim($data['first_name'] . ' ' . $data['last_name']);
        $data['zip'] = strtoupper(preg_replace('/\s+/', ' ', trim($data['zip'])));
        $data['city'] = trim($data['city'] ?? $data['state'] ?? 'UK');
        unset($data['first_name'], $data['last_name']);

        $shipping = 4.99;

        $order = DB::transaction(function () use ($data, $summary, $shipping) {
            $order = Order::create([
                ...$data,
                'order_number' => $this->orderNumber(),
                'subtotal' => $summary['subtotal'],
                'shipping_total' => $shipping,
                'total' => $summary['subtotal'] + $shipping,
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'tracking_status' => 'placed',
                'tracking_number' => $this->royalMailTrackingNumber(),
            ]);

            foreach ($summary['items'] as $item) {
                $product = $item['product'];
                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'product_image' => $product->image,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'line_total' => $item['line_total'],
                ]);

            }

            return $order;
        });

        $cart->clear();
        session(['latest_order_id' => $order->id]);
        Mail::to($order->email)->send(new OrderPlacedMail($order->load('items')));

        return redirect()->route('frontend.order-success');
    }

    private function filters(Request $request): array
    {
        $minPrice = $request->query('min_price');
        $maxPrice = $request->query('max_price');
        $sort = $request->query('sort', 'latest');

        if (is_numeric($minPrice) && is_numeric($maxPrice) && (float) $minPrice > (float) $maxPrice) {
            [$minPrice, $maxPrice] = [$maxPrice, $minPrice];
        }

        if (! in_array($sort, ['latest', 'price_asc', 'price_desc', 'name'], true)) {
            $sort = 'latest';
        }

        if ($sort === 'latest' && (is_numeric($minPrice) || is_numeric($maxPrice))) {
            $sort = 'price_asc';
        }

        return [
            'q' => trim((string) $request->query('q', '')),
            'category' => trim((string) $request->query('category', '')),
            'min_price' => $minPrice,
            'max_price' => $maxPrice,
            'sort' => $sort,
        ];
    }

    private function filteredProducts(array $filters)
    {
        return Product::with('category')
            ->where('status', 'active')
            ->when($filters['q'], function ($query, string $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhere('short_description', 'like', "%{$search}%")
                        ->orWhereHas('category', fn ($query) => $query->where('name', 'like', "%{$search}%")->orWhere('slug', 'like', "%{$search}%"));
                });
            })
            ->when($filters['category'], fn ($query, string $slug) => $query->whereHas('category', fn ($query) => $query->where('slug', $slug)))
            ->when(is_numeric($filters['min_price']), fn ($query) => $query->whereRaw('COALESCE(sale_price, price) >= ?', [(float) $filters['min_price']]))
            ->when(is_numeric($filters['max_price']), fn ($query) => $query->whereRaw('COALESCE(sale_price, price) <= ?', [(float) $filters['max_price']]))
            ->orderByRaw('stock > 0 desc')
            ->when($filters['sort'] === 'price_asc', fn ($query) => $query->orderByRaw('COALESCE(sale_price, price) asc'))
            ->when($filters['sort'] === 'price_desc', fn ($query) => $query->orderByRaw('COALESCE(sale_price, price) desc'))
            ->when($filters['sort'] === 'name', fn ($query) => $query->orderBy('name'))
            ->when(! in_array($filters['sort'], ['price_asc', 'price_desc', 'name'], true), fn ($query) => $query->latest());
    }

    private function cartPayload(array $summary): array
    {
        return [
            'item_count' => $summary['item_count'],
            'subtotal' => $summary['subtotal'],
            'shipping' => $summary['shipping'],
            'total' => $summary['total'],
            'is_empty' => $summary['is_empty'],
            'items' => $summary['items']->map(fn (array $item) => [
                'id' => $item['product']->id,
                'name' => $item['product']->name,
                'meta' => $item['product']->category?->name ?? 'Product',
                'image' => url($item['product']->image ?: 'frontend/assets/images/product-bottle.png'),
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'line_total' => $item['line_total'],
            ])->values(),
        ];
    }

    private function orderNumber(): string
    {
        do {
            $number = 'AR' . now()->format('ymd') . strtoupper(Str::random(5));
        } while (Order::where('order_number', $number)->exists());

        return $number;
    }

    private function royalMailTrackingNumber(): string
    {
        do {
            $number = 'RM' . now()->format('ymd') . strtoupper(Str::random(5)) . 'GB';
        } while (Order::where('tracking_number', $number)->exists());

        return $number;
    }

    private function cartProduct(string $value): Product
    {
        return Product::where('id', $value)->orWhere('slug', $value)->firstOrFail();
    }
}
