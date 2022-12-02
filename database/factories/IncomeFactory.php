<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Income;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Income>
 */
class IncomeFactory extends Factory
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
            'type' => $this->faker->randomElement(Income::TYPES_INCOME),
            'description' => $this->faker->paragraph(2),
            'amount' => $this->faker->numerify('######'),
            'branch_id' => $this->faker->randomElement($branches)
        ];
    }
}
