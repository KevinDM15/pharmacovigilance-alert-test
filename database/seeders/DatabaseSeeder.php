<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Medication;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    private const RECALLED_LOT = '951357';

    public function run(): void
    {
        User::factory()->create([
            'name' => 'Pharmacovigilance Admin',
            'username' => env('SEED_ADMIN_USERNAME', 'admin'),
            'password' => env('SEED_ADMIN_PASSWORD', 'password'),
        ]);

        $recalledMedication = Medication::factory()->create([
            'name' => 'Progesterone Cream 10%',
            'lot_number' => self::RECALLED_LOT,
        ]);

        Customer::factory(8)
            ->create()
            ->each(function (Customer $customer, int $index) use ($recalledMedication) {
                $purchaseDate = $index < 5
                    ? now()->subDays(fake()->numberBetween(1, 29))
                    : now()->subMonths(fake()->numberBetween(2, 6));

                $order = Order::factory()->create([
                    'customer_id' => $customer->id,
                    'purchase_date' => $purchaseDate->format('Y-m-d'),
                ]);

                $order->items()->create(['medication_id' => $recalledMedication->id]);
            });

        Medication::factory(5)->create();
        Order::factory(15)
            ->has(OrderItem::factory()->count(2), 'items')
            ->create();
    }
}
