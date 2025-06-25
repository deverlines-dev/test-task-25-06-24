<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'rowid' => random_bytes(16), // для тестовых данных, т.к str::random генерится быстрее
            'name' => fake()->name(),
            'password' => null,
        ];
    }

    public function withScores(int $count): static
    {
        return $this->has(new UserScoreFactory()->count($count), 'scores');
    }

    public function withProperties(): static
    {
        return $this->has(new UserPropertyFactory()->count(1), 'properties');
    }
}
