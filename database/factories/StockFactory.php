<?php

namespace Database\Factories;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Stock>
 */
class StockFactory extends Factory
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
            'name' => $this->faker->lexify('????????'),
            'mitra_name' => $this->faker->lexify('????????'),
            'mitra_wa' => $this->faker->numerify('628##########'),
            'stock_number' => $this->faker->randomNumber(3),
            'branch_id' => $this->faker->randomElement($branches),
            'image' => 'default.png'
        ];
    }
}
