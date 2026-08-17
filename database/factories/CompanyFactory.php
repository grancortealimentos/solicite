<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Company>
 */
class CompanyFactory extends Factory
{
    protected $model = Company::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'is_active' => true,
            'code' => fake()->unique()->numerify('FIL###'),
            'name' => fake()->company(),
            'trade_name' => fake()->companySuffix(),
            'cnpj' => fake()->unique()->numerify('##############'),
            'ie' => fake()->numerify('##########'),
            'zip' => fake()->numerify('########'),
            'street' => fake()->streetName(),
            'number' => fake()->buildingNumber(),
            'district' => fake()->word(),
            'city' => fake()->city(),
            'state' => fake()->stateAbbr(),
            'complement' => null,
            'geolocation' => null,
        ];
    }
}
