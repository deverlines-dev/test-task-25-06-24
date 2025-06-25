<?php

namespace Database\Seeders;

use Database\Factories\BookFactory;
use Database\Factories\UserFactory;
use Illuminate\Database\Seeder;
use Laravel\Prompts\Output\ConsoleOutput;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $console = new ConsoleOutput();

        $console->writeln('seed users');
        new UserFactory()
            ->count(100)
            ->withScores(fake()->numberBetween(10, 20))
            ->withProperties()
            ->create();

        $console->writeln('seed books');
        new BookFactory()
            ->count(200)
            ->withUserBookmark(fake()->numberBetween(10, 20))
            ->create();
    }
}
