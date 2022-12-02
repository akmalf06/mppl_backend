<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Spend;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Spend>
 */
class SpendFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $branches = Branch::pluck('id')->all();
        return [
            'type' => $this->faker->randomElement(Spend::TYPES_SPEND),
            'description' => $this->faker->paragraph(2),
            'amount' => $this->faker->numerify('######'),
            'branch_id' => $this->faker->randomElement($branches)
        ];
    }
}
