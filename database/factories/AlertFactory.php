<?php

namespace Database\Factories;

use App\Models\Alert;
use App\Models\Customer;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Alert>
 */
class AlertFactory extends Factory
{
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'order_id' => Order::factory(),
            'user_id' => User::factory(),
            'sent_at' => fake()->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
