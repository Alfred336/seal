<?php

namespace Database\Factories;

use App\Enums\CallRequestStatus;
use App\Models\CallRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CallRequest>
 */
class CallRequestFactory extends Factory
{
    protected $model = CallRequest::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'full_name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'preferred_date' => fake()->dateTimeBetween('+1 day', '+30 days'),
            'notes' => fake()->optional()->sentence(),
            'status' => CallRequestStatus::Pending,
            'ip_address' => fake()->ipv4(),
        ];
    }
}
