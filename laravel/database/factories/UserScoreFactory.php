<?php

namespace Database\Factories;

use App\Models\UserScore;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserScore>
 */
class UserScoreFactory extends Factory
{
    protected $model = UserScore::class;

    public function definition(): array
    {
        return [
            'score' => fake()->numberBetween(-100, 100),
        ];
    }
}
