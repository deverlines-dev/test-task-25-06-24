<?php

namespace Tests\Unit;

use App\Data\TopScopeData;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Collection;
use Laravel\Prompts\Output\ConsoleOutput;
use Tests\TestCase;

/**
 * Тест запроса который:
 * Возвращает top 10 и bottom 10 user_id по сумме score за 7 суток,
 * С указанием ранга и сортировкой по сумме score + место конкретного user_id в получившейся общей рейтинговой таблице.
 *
 *
 * Один из вариантов решения получения ранга и суммы score:
 * Добавить поля в табличке для пользователя, либо создать табличку рейтинга
 * И при обновлении данных обновлять табличку, тогда не нужно делать перерасчёты при получении данных
 *
 * Так же можно вести статистикс табличку по дням (для получения за последние N дней)
 *
 * Ещё есть вариант с кешированием (Создаём кеш каждый день и рассчитывать нужно будет только сегодняшний)
 *
 *
 *
 * Два варианта накидал, v1 проще, v2 с новым функционалом от mysql 8
 *
 * Немного удивило время выполнения первого варианта, думал он будет медленнее
 * Второй вариант оказался медленнее, но более щадящий к оперативной памяти
 * В первом варианте можно так-же пройтись через курсор вместо $query->get();
 *
 * v1
 * timeExecution: 0.0749 sec
 * memoryExecution: 0.7328 mb
 * memoryPeakExecution: 1.1658 mb
 *
 * v2
 * timeExecution: 0.1294 sec
 * memoryExecution: 0.0150 mb
 * memoryPeakExecution: 0.0879 mb
 */
class Task1Test extends TestCase
{
    private DatabaseManager $db;

    /**
     * @noinspection PhpUnhandledExceptionInspection
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->db = $this->app->make(DatabaseManager::class);
    }

    public function test_task_v1(): void
    {
        $console = new ConsoleOutput();

        $time = hrtime(true);
        $memory = memory_get_usage();
        $memoryPeak = memory_get_peak_usage();

        $lastDaysDate = now()->toImmutable()->addDays(-7);

        $query = $this->db->table('user_scores')
            ->select('user_id', $this->db->raw('SUM(score) AS total_score'))
            ->where('created_at', '>=', $lastDaysDate)
            ->orderByDesc('total_score')
            ->groupBy('user_id');

        $scopes = $query->get();

        /**
         * @var TopScopeData[]|Collection<TopScopeData> $top10
         * @var TopScopeData[]|Collection<TopScopeData> $bottom10
         */

        // тут дубли кода, но в данном случае не важно
        $top10 = $scopes->sortByDesc('total_score')->take(10)->map(function (object $scope, int $key) {
            return new TopScopeData(
                userId: $scope->user_id,
                totalScore: $scope->total_score,
                rank: $key+1 // назначаем ручками, т.к правильно отсортировано
            );
        });

        $this->assertCount(10, $top10); // 10 элементом

        $top10RangeKeys = range(0, 9);
        foreach ($top10RangeKeys as $key) {
            $top10Element = $top10[$key];
            $rank = $key+1;

            $this->assertEquals($rank, $top10Element->rank); // ранг соответствует
        }

        $previousScore = null;
        foreach ($top10 as $item) {
            if ($previousScore !== null) {
                $this->assertGreaterThanOrEqual($item->totalScore, $previousScore); // предыдущий больше текущего
            }
            $previousScore = $item->totalScore;
        }

        $bottom10 = $scopes->sortBy('total_score')->take(10)->map(function (object $scope, int $key) {
            return new TopScopeData(
                userId: $scope->user_id,
                totalScore: $scope->total_score,
                rank: $key+1 // назначаем ручками, т.к правильно отсортировано
            );
        })->reverse();

        $bottom10RangeKeys = range($scopes->count() - 10, $scopes->count() - 1);
        $this->assertCount(10, $bottom10); // 10 элементом
        $this->assertCount(10, $bottom10RangeKeys); // 10 элементом

        foreach ($bottom10RangeKeys as $key) { // последние 10 элементов из общей статистики
            $bottom10Element = $bottom10[$key];
            $rank = $key+1;

            $this->assertEquals($rank, $bottom10Element->rank); // ранг соответствует
        }

        $previousScore = null;
        foreach ($bottom10 as $item) {
            if ($previousScore !== null) {
                $this->assertGreaterThanOrEqual($item->totalScore, $previousScore); // предыдущий больше текущего
            }
            $previousScore = $item->totalScore;
        }

        $timeExecution = (hrtime(true) - $time) / 1e+9 . ' sec';
        $memoryExecution = (memory_get_usage() - $memory) / 1024 / 1024 . 'mb';
        $memoryPeakExecution = (memory_get_peak_usage() - $memoryPeak) / 1024 / 1024 . 'mb';

        $console->writeln("task 1 - v1");
        $console->writeln("timeExecution: $timeExecution");
        $console->writeln("memoryExecution: $memoryExecution");
        $console->writeln("memoryPeakExecution: $memoryPeakExecution");
    }

    public function test_task_v2(): void
    {
        $console = new ConsoleOutput();

        $time = hrtime(true);
        $memory = memory_get_usage();
        $memoryPeak = memory_get_peak_usage();

        $lastDaysDate = now()->toImmutable()->addDays(-7);

        $userScoresQuery = $this->db->table('user_scores')
            ->select('user_id', $this->db->raw('SUM(score) AS total_score'))
            ->where('created_at', '>=', $lastDaysDate)
            ->groupBy('user_id');

        $q = $this->db->query()
            ->fromSub($userScoresQuery, 'scores')
            ->select(
                'user_id',
                'total_score',
                $this->db->raw('ROW_NUMBER() OVER (ORDER BY total_score DESC) AS `rank`')
            );

        /**
         * @var TopScopeData[]|Collection<TopScopeData> $top10
         * @var TopScopeData[]|Collection<TopScopeData> $bottom10
         */

        // 1) Первый проход — находим максимальный ранг (maxRank)
        $maxRank = 0;
        foreach ($q->cursor() as $scope) {
            if ($scope->rank > $maxRank) {
                $maxRank = $scope->rank;
            }
        }

        $top10 = collect();
        $bottomRows = [];

        foreach ($q->cursor() as $scope) {
            if ($scope->rank <= 10) {
                $top10->push(new TopScopeData(
                    userId: $scope->user_id,
                    totalScore: $scope->total_score,
                    rank: $scope->rank
                ));
            }

            if ($scope->rank > $maxRank - 10) {
                $bottomRows[] = $scope;
            }
        }

        $bottom10 = collect($bottomRows)->sortBy('rank')->map(function (object $scope) {
            return new TopScopeData(
                userId: $scope->user_id,
                totalScore: $scope->total_score,
                rank: $scope->rank
            );
        });

        //

        $this->assertCount(10, $top10); // 10 элементом

        $top10RangeKeys = range(0, 9);
        foreach ($top10RangeKeys as $key) {
            $top10Element = $top10[$key];
            $rank = $key+1;

            $this->assertEquals($rank, $top10Element->rank); // ранг соответствует
        }

        $previousScore = null;
        foreach ($top10 as $item) {
            if ($previousScore !== null) {
                $this->assertGreaterThanOrEqual($item->totalScore, $previousScore); // предыдущий больше текущего
            }
            $previousScore = $item->totalScore;
        }

        $this->assertCount(10, $bottom10); // 10 элементом

        $previousScore = null;
        foreach ($bottom10 as $item) {
            if ($previousScore !== null) {
                $this->assertGreaterThanOrEqual($item->totalScore, $previousScore); // предыдущий больше текущего
            }
            $previousScore = $item->totalScore;
        }

        $timeExecution = (hrtime(true) - $time) / 1e+9 . ' sec';
        $memoryExecution = (memory_get_usage() - $memory) / 1024 / 1024 . 'mb';
        $memoryPeakExecution = (memory_get_peak_usage() - $memoryPeak) / 1024 / 1024 . 'mb';

        $console->writeln("task 1 - v2");
        $console->writeln("timeExecution: $timeExecution");
        $console->writeln("memoryExecution: $memoryExecution");
        $console->writeln("memoryPeakExecution: $memoryPeakExecution");
    }
}
