<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    protected $model = Project::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->catchPhrase(),
            'industry' => fake()->randomElement(['Healthcare', 'Finance', 'E-commerce', 'Education']),
            'tech_stack' => 'Laravel, React, PostgreSQL',
            'description' => fake()->paragraph(),
            'client_name' => fake()->company(),
            'outcome' => fake()->sentence(),
            'live_url' => fake()->url(),
            'featured' => false,
            'active' => true,
            'sort_order' => fake()->numberBetween(0, 100),
            'completed_at' => fake()->dateTimeBetween('-2 years'),
        ];
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'featured' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'active' => false,
        ]);
    }
}
