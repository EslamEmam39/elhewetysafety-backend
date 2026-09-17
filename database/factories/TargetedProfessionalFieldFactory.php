<?php

namespace Database\Factories;

use App\Models\TargetedProfessionalField;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<TargetedProfessionalField> */
class TargetedProfessionalFieldFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code' => fake()->unique()->slug(2),
            'name_en' => fake()->words(3, true),
            'name_ar' => fake()->words(3, true),
            'is_active' => true,
            'sort_order' => fake()->numberBetween(1, 100),
        ];
    }
}