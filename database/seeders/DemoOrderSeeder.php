<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class DemoOrderSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::query()->where('status', 'active')->get();

        if ($products->isEmpty()) {
            throw new RuntimeException('No active products found. Seed the catalogue before demo orders.');
        }

        $firstNames = ['Oliver', 'George', 'Harry', 'Jack', 'Noah', 'Charlie', 'Jacob', 'Alfie', 'Muhammad', 'Thomas', 'Amelia', 'Olivia', 'Isla', 'Ava', 'Mia', 'Emily', 'Sophia', 'Grace', 'Lily', 'Freya'];
        $lastNames = ['Smith', 'Jones', 'Taylor', 'Brown', 'Williams', 'Wilson', 'Johnson', 'Davies', 'Patel', 'Robinson', 'Wright', 'Thompson', 'Evans', 'Walker', 'White'];
        $cities = [
            ['London', 'Greater London', 'SW1A 1AA'],
            ['Manchester', 'Greater Manchester', 'M1 1AE'],
            ['Birmingham', 'West Midlands', 'B1 1BB'],
            ['Leeds', 'West Yorkshire', 'LS1 1UR'],
            ['Liverpool', 'Merseyside', 'L1 8JQ'],
            ['Bristol', 'Somerset', 'BS1 4ST'],
            ['Glasgow', 'Scotland', 'G1 1XW'],
            ['Edinburgh', 'Scotland', 'EH1 1YZ'],
        ];
        $trackingStatuses = [
            'delivered', 'delivered', 'delivered', 'delivered',
            'dispatched', 'out_for_delivery', 'packed', 'processing',
            'placed', 'cancelled',
        ];
        $paymentMethods = ['card', 'card', 'card', 'bank_transfer', 'cash_on_delivery'];

        mt_srand(20260730);

        DB::transaction(function () use ($products, $firstNames, $lastNames, $cities, $trackingStatuses, $paymentMethods): void {
            Order::query()
                ->where('order_number', 'like', 'DEMO-%')
                ->each(fn (Order $order) => $order->delete());

            for ($index = 1; $index <= 210; $index++) {
                $firstName = $firstNames[array_rand($firstNames)];
                $lastName = $lastNames[array_rand($lastNames)];
                [$city, $state, $postcode] = $cities[array_rand($cities)];
                $createdAt = Carbon::now()
                    ->subDays(mt_rand(0, 89))
                    ->setTime(mt_rand(8, 21), mt_rand(0, 59), mt_rand(0, 59));
                $trackingStatus = $trackingStatuses[array_rand($trackingStatuses)];
                $paymentStatus = $trackingStatus === 'cancelled'
                    ? (mt_rand(0, 1) ? 'refunded' : 'failed')
                    : (mt_rand(1, 100) <= 82 ? 'paid' : 'unpaid');
                $paymentMethod = $paymentMethods[array_rand($paymentMethods)];
                $itemCount = mt_rand(1, 3);
                $selectedProducts = $products->shuffle()->take($itemCount);
                $subtotal = 0;
                $lines = [];

                foreach ($selectedProducts as $product) {
                    $quantity = mt_rand(1, 3);
                    $unitPrice = (float) ($product->sale_price ?: $product->price);
                    $lineTotal = $unitPrice * $quantity;
                    $subtotal += $lineTotal;
                    $lines[] = compact('product', 'quantity', 'unitPrice', 'lineTotal');
                }

                $shipping = $subtotal >= 100 ? 0 : 4.99;
                $orderStatus = match ($trackingStatus) {
                    'delivered' => 'delivered',
                    'dispatched', 'out_for_delivery' => 'shipped',
                    'cancelled' => 'cancelled',
                    'placed' => 'pending',
                    default => 'processing',
                };

                $order = Order::create([
                    'order_number' => 'DEMO-' . str_pad((string) $index, 5, '0', STR_PAD_LEFT),
                    'customer_name' => "{$firstName} {$lastName}",
                    'email' => strtolower("{$firstName}.{$lastName}{$index}@example.com"),
                    'phone' => '07' . mt_rand(100000000, 999999999),
                    'address' => mt_rand(1, 220) . ' ' . $lastNames[array_rand($lastNames)] . ' Street',
                    'city' => $city,
                    'state' => $state,
                    'zip' => $postcode,
                    'country' => 'United Kingdom',
                    'shipping_method' => $shipping > 0 ? 'flat_rate' : 'free_shipping',
                    'payment_method' => $paymentMethod,
                    'payment_status' => $paymentStatus,
                    'subtotal' => $subtotal,
                    'shipping_total' => $shipping,
                    'total' => $subtotal + $shipping,
                    'status' => $orderStatus,
                    'tracking_status' => $trackingStatus,
                    'tracking_number' => in_array($trackingStatus, ['dispatched', 'out_for_delivery', 'delivered'], true)
                        ? 'RM' . mt_rand(100000000, 999999999) . 'GB'
                        : null,
                    'order_notes' => mt_rand(1, 100) <= 12 ? 'Demo customer requested careful packaging.' : null,
                ]);

                $order->forceFill(['created_at' => $createdAt, 'updated_at' => $createdAt])->save();

                foreach ($lines as $line) {
                    $item = $order->items()->create([
                        'product_id' => $line['product']->id,
                        'product_name' => $line['product']->name,
                        'product_sku' => $line['product']->sku,
                        'product_image' => $line['product']->image,
                        'quantity' => $line['quantity'],
                        'unit_price' => $line['unitPrice'],
                        'line_total' => $line['lineTotal'],
                    ]);
                    $item->forceFill(['created_at' => $createdAt, 'updated_at' => $createdAt])->save();
                }
            }
        });

        $this->command?->info('210 realistic demo orders created for the dashboard.');
    }
}
