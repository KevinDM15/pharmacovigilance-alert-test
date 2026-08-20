<?php

namespace Database\Factories;

use App\Models\Medication;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Medication>
 */
class MedicationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement([
                'Progesterone Cream 10%',
                'Testosterone Gel 5%',
                'Ketamine Nasal Spray',
                'Melatonin Sublingual 5mg',
                'Metronidazole Vaginal Gel',
                'Tretinoin Cream 0.05%',
            ]),
            'lot_number' => fake()->unique()->numerify('######'),
        ];
    }
}
