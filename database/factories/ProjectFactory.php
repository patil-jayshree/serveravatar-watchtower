<?php

namespace Database\Factories;

use App\Enums\Project\ProjectEnvironment;
use App\Enums\Project\ProjectFramework;
use App\Enums\Project\ProjectStatus;
use App\Models\Organization;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'name' => fake()->words(3, true),
            'description' => fake()->optional()->sentence(),
            'framework' => fake()->randomElement(ProjectFramework::values()),
            'environment' => fake()->randomElement(ProjectEnvironment::values()),
            'status' => fake()->randomElement(ProjectStatus::values()),
        ];
    }

    /**
     * Indicate that the project is for Laravel.
     */
    public function laravel(): static
    {
        return $this->state(fn (array $attributes) => [
            'framework' => ProjectFramework::LARAVEL->value,
        ]);
    }

    /**
     * Indicate that the project is in production.
     */
    public function production(): static
    {
        return $this->state(fn (array $attributes) => [
            'environment' => ProjectEnvironment::PRODUCTION->value,
        ]);
    }

    /**
     * Indicate that the project is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ProjectStatus::ACTIVE->value,
        ]);
    }
}
