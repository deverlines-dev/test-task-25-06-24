<?php

use App\Http\Repositories\UserRepository;
use App\Models\User;
use App\Models\UserBookmark;
use App\Models\UserProperty;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Prompts\Output\ConsoleOutput;
use Tests\TestCase;

/**
 * В БД есть таблицы (books, users, bookmarks) + таблица свойств пользователей user_properties, в которой хранится user_id, key и value.
 *
 * Напишите код на Laravel для получения всех пользователей с их имейлами и номерами телефона,
 * которые читают книгу с id 123 (у которых есть закладка в этой книге).
 *
 * Подумайте, как можно оптимизировать код, если предположить, что в таблице пользователей может быть 10 миллионов строк.
 *
 *
 *
 * task 3
 *
 *
 * Лучше конечно добавить пагинацию
 * Так же можно аналогично первому заданию сделать через DB запросы
 * Можно отключить события БД и моделей при получении
 *
 *  Через Get
 *  timeExecution: 0.0240 sec
 *  memoryExecution: 0.4363 mb
 *  memoryPeakExecution: 0.8476 mb
 *
 *  Через cursor
 *  timeExecution: 0.0182 sec
 *  memoryExecution: 0.4508 mb
 *  memoryPeakExecution: 0.7106 mb
 */
class Task3Test extends TestCase
{
    private DatabaseManager $db;
    private UserRepository $userRepository;

    /**
     * @noinspection PhpUnhandledExceptionInspection
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->db = $this->app->make(DatabaseManager::class);
        $this->userRepository = $this->app->make(UserRepository::class);
    }

    public function test_task_v1(): void
    {
        $console = new ConsoleOutput();

        $time = hrtime(true);
        $memory = memory_get_usage();
        $memoryPeak = memory_get_peak_usage();

        $bookId = 123;

        $query = $this->userRepository->query()
            ->whereHas('bookmarks', function ($query) use ($bookId) {
                $query->where('book_id', $bookId);
            })
            ->with('bookmarks', function ($query) use ($bookId) {
                $query->where('book_id', $bookId);
            })
            ->with([
                'properties' => function (HasMany $query) use ($bookId) {
                    $query->where('property_key', 'email')
                        ->orWhere('property_key', 'phone');
                },
            ])
            ->select(['id']);

        $cursor = $query->cursor();

        $cursor->each(function (User $user) use ($bookId) {
            $user->bookmarks->each(function (UserBookmark $userBookmark) use ($bookId) {
                $this->assertEquals($bookId, $userBookmark->book_id);
            });

            $user->properties->each(function (UserProperty $userProperty) {

            });
        });

        $timeExecution = (hrtime(true) - $time) / 1e+9 . ' sec';
        $memoryExecution = (memory_get_usage() - $memory) / 1024 / 1024 . 'mb';
        $memoryPeakExecution = (memory_get_peak_usage() - $memoryPeak) / 1024 / 1024 . 'mb';

        $console->writeln("task 3 v1");
        $console->writeln("timeExecution: $timeExecution");
        $console->writeln("memoryExecution: $memoryExecution");
        $console->writeln("memoryPeakExecution: $memoryPeakExecution");

        $this->assertTrue(true);
    }

}
