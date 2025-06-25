<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\UserBookmark;
use Database\Factories\BookFactory;
use Database\Factories\UserFactory;
use Database\Factories\UserPropertyFactory;
use Illuminate\Database\Seeder;
use Laravel\Prompts\Output\ConsoleOutput;

class DatabaseSeeder extends Seeder
{
    /**
     * Тут слишком накидал, для рабочих такой скрипт слишком непонятный и долгий
     */
    public function run(): void
    {
        $console = new ConsoleOutput();

        $console->writeln('seed users');
        new UserFactory()
            ->count(1000)
            ->withScores(fake()->numberBetween(50, 100))
            ->withProperties()
            ->create();

        $console->writeln('seed books');
        $books = new BookFactory()
            ->count(1000)
            ->withUserBookmark(fake()->numberBetween(50, 100))
            ->create();
        $books->each(function (Book $book) {
            $book->bookmarks->each(function (UserBookmark $bookmark) {

                $definition = new UserPropertyFactory()->definition();

                $bookmark->user->properties()->create($definition);
            });
        });
    }
}
