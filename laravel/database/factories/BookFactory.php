<?php

namespace Database\Factories;

use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Book>
 */
class BookFactory extends Factory
{
    protected $model = Book::class;

    public function definition(): array
    {
        return [
            'rowid' => random_bytes(16), // для тестовых данных, т.к str::random генерится быстрее
            'title' => fake()->title(),
            'description' => fake()->text(),
            'pages_count' => fake()->numberBetween(1, 100),
        ];
    }

    public function withUserBookmark(int $count): static
    {
        return $this->has(
            new UserBookmarkFactory()
                ->count($count),
            'bookmarks'
        );
    }
}
