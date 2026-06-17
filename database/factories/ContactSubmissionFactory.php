<?php

namespace Database\Factories;

use App\Enums\ContactSubmissionStatus;
use App\Models\ContactSubmission;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ContactSubmission>
 */
class ContactSubmissionFactory extends Factory
{
    protected $model = ContactSubmission::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'company' => fake()->optional()->company(),
            'phone' => fake()->optional()->phoneNumber(),
            'project_type' => fake()->randomElement(['web', 'mobile', 'consulting']),
            'message' => fake()->paragraph(),
            'ip_address' => fake()->ipv4(),
            'status' => ContactSubmissionStatus::New,
            'submitted_at' => now(),
        ];
    }
}
