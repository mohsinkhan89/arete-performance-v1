<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FrontendController extends Controller
{
    public function index()
    {
        return view('frontend.index', [
            'categories' => Category::withCount('products')->where('status', 'active')->orderBy('sort_order')->take(8)->get(),
            'featuredProducts' => Product::with('category')->where('status', 'active')->where('is_featured', true)->latest()->take(5)->get(),
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

    public function orderSuccess()
    {
        $orderId = session('latest_order_id');
        $order = $orderId ? Order::with('items')->find($orderId) : null;

        return view('frontend.order-success', [
            'order' => $order,
        ]);
    }

    public function productDetails()
    {
        return view('frontend.product-details');
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

    public function shop(Request $request)
    {
        $filters = $this->filters($request);
        $products = $this->filteredProducts($filters)->paginate(12)->withQueryString();

        return view('frontend.shop', [
            'categories' => Category::withCount(['products' => fn ($query) => $query->where('status', 'active')])->where('status', 'active')->orderBy('sort_order')->get(),
            'products' => $products,
            'filters' => $filters,
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

        return response()->json($this->cartPayload($cart->add($product, (int) $request->input('quantity', 1))));
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
            'email' => ['required', 'email', 'max:255'],
            'customer_name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:120'],
            'state' => ['nullable', 'string', 'max:120'],
            'zip' => ['nullable', 'string', 'max:40'],
            'country' => ['required', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:40'],
            'shipping_method' => ['required', 'in:standard,express'],
            'payment_method' => ['required', 'in:card,paypal,other'],
        ]);

        $shipping = $data['shipping_method'] === 'express' ? 19.99 : 9.99;

        $order = DB::transaction(function () use ($data, $summary, $shipping) {
            $order = Order::create([
                ...$data,
                'order_number' => $this->orderNumber(),
                'subtotal' => $summary['subtotal'],
                'shipping_total' => $shipping,
                'total' => $summary['subtotal'] + $shipping,
                'status' => 'pending',
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

                $product->decrement('stock', min($product->stock, $item['quantity']));
            }

            return $order;
        });

        $cart->clear();
        session(['latest_order_id' => $order->id]);

        return redirect()->route('frontend.order-success');
    }

    private function filters(Request $request): array
    {
        return [
            'q' => trim((string) $request->query('q', '')),
            'category' => trim((string) $request->query('category', '')),
            'min_price' => $request->query('min_price'),
            'max_price' => $request->query('max_price'),
            'sort' => $request->query('sort', 'latest'),
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

    private function cartProduct(string $value): Product
    {
        return Product::where('id', $value)->orWhere('slug', $value)->firstOrFail();
    }
}
