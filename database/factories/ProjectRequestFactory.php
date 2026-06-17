<?php

namespace Database\Factories;

use App\Enums\ProjectRequestStatus;
use App\Models\ProjectRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProjectRequest>
 */
class ProjectRequestFactory extends Factory
{
    protected $model = ProjectRequest::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'full_name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'project_type' => fake()->randomElement(['web', 'mobile', 'api', 'consulting']),
            'details' => fake()->paragraph(),
            'status' => ProjectRequestStatus::New,
            'ip_address' => fake()->ipv4(),
        ];
    }
}
