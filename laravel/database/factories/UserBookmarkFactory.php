<?php

namespace Database\Factories;

use App\Models\UserBookmark;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserBookmark>
 */
class UserBookmarkFactory extends Factory
{
    protected $model = UserBookmark::class;

    public function definition(): array
    {
        return [
            'rowid' => random_bytes(16), // для тестовых данных, т.к str::random генерится быстрее
            'bookmark' => fake()->text(),
            'user_id' => new UserFactory()->create()->id,
        ];
    }
}
