<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;

class CartService
{
    private const SESSION_KEY = 'cart.items';

    public function raw(): array
    {
        return session(self::SESSION_KEY, []);
    }

    public function add(Product $product, int $quantity = 1): array
    {
        $items = $this->raw();
        $quantity = max(1, $quantity);
        $items[$product->id] = min(99, ($items[$product->id] ?? 0) + $quantity);
        session([self::SESSION_KEY => $items]);

        return $this->summary();
    }

    public function update(Product $product, int $quantity): array
    {
        $items = $this->raw();

        if ($quantity <= 0) {
            unset($items[$product->id]);
        } else {
            $items[$product->id] = min(99, $quantity);
        }

        session([self::SESSION_KEY => $items]);

        return $this->summary();
    }

    public function remove(Product $product): array
    {
        $items = $this->raw();
        unset($items[$product->id]);
        session([self::SESSION_KEY => $items]);

        return $this->summary();
    }

    public function clear(): array
    {
        session()->forget(self::SESSION_KEY);

        return $this->summary();
    }

    public function summary(): array
    {
        $cart = $this->raw();
        $productIds = array_keys($cart);

        /** @var Collection<int, Product> $products */
        $products = Product::with('category')
            ->whereIn('id', $productIds)
            ->where('status', 'active')
            ->get()
            ->keyBy('id');

        $items = collect($cart)
            ->map(function (int $quantity, int|string $productId) use ($products) {
                $product = $products->get((int) $productId);

                if (! $product) {
                    return null;
                }

                $unitPrice = (float) ($product->sale_price ?: $product->price);
                $quantity = max(1, min(99, $quantity));

                return [
                    'product' => $product,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'line_total' => $unitPrice * $quantity,
                ];
            })
            ->filter()
            ->values();

        $this->syncFromItems($items);

        $subtotal = (float) $items->sum('line_total');
        $itemCount = (int) $items->sum('quantity');
        $shipping = $itemCount > 0 ? 9.99 : 0.0;
        $total = $subtotal + $shipping;

        return [
            'items' => $items,
            'item_count' => $itemCount,
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'total' => $total,
            'is_empty' => $items->isEmpty(),
        ];
    }

    private function syncFromItems(Collection $items): void
    {
        session([
            self::SESSION_KEY => $items
                ->mapWithKeys(fn (array $item) => [$item['product']->id => $item['quantity']])
                ->all(),
        ]);
    }
}
