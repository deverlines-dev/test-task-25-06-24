<?php

use Illuminate\Database\DatabaseManager;
use Laravel\Prompts\Output\ConsoleOutput;
use Tests\TestCase;

/**
 *
 * Sql скрипт который:
 *
 * Возвращает top 10 и bottom 10 user_id по сумме score за 7 суток,
 * С указанием ранга и сортировкой по сумме score + место конкретного user_id в получившейся общей рейтинговой таблице.
 *
 *  Проблемы предыдущей реализации:
 *      Проблема в том, что получаем ВСЕ элементы, далее их отсееваем и делаем перерасчёты на уровне php кода.
 *      В связи с этим: большое потребление ресурсов (если сравнивать с одним запросом), далее тяжеловато будет поддерживать
 *
 * timeExecution: 0.1662 sec
 * memoryExecution: 0.5141 mb
 * memoryPeakExecution: 0.7823 mb
 */
class Task1SqlTest extends TestCase
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

    public function test_task_sql(): void
    {
        $console = new ConsoleOutput();

        $time = hrtime(true);
        $memory = memory_get_usage();
        $memoryPeak = memory_get_peak_usage();

        $sql =
            /** @lang MySQL */
            "
            -- last_days_scores --
            SELECT
                last_days_scores.user_id,
                last_days_scores.total_score,
                last_days_scores.rank,
                CASE
                    WHEN last_days_scores.rank <= 10 THEN 'top_10'
                    WHEN last_days_scores.rank > total_counts.total_rows - 10 THEN 'bottom_10'
                END AS `group`
            FROM (
                -- rank --
                SELECT
                    `user_id`,
                    SUM(`score`) AS `total_score`,
                    RANK() OVER (ORDER BY SUM(`score`) DESC) AS `rank`
                FROM `user_scores`
                WHERE `created_at` >= :last_days_date_scores
                GROUP BY `user_id`
            ) AS last_days_scores

            -- total_counts --
            CROSS JOIN (
                SELECT COUNT(DISTINCT `user_id`) AS `total_rows`
                FROM `user_scores`
                WHERE `created_at` >= :last_days_date_rows
            ) AS total_counts
            WHERE
                last_days_scores.rank <= 10 OR last_days_scores.rank > total_counts.total_rows - 10

            ORDER BY last_days_scores.rank ASC;
        ";

        $lastDaysDate = now()->toImmutable()->subDays(7)->toDateTimeString();

        /**
         * Тут лучше через Dto, пока для подсказок в ide так описал
         *
         * @var object{
         *       user_id: int,
         *       total_score: string|int,
         *       rank: int,
         *       group: string,
         *  } $rank
         *
         * @var object{
         *      user_id: int,
         *      total_score: string|int,
         *      rank: int,
         *      group: string,
         * }[] $ranksData
         */
        $ranksData = $this->db->select($sql, [
            'last_days_date_scores' => $lastDaysDate, // можно и внутри sql через NOW() - INTERVAL 7 DAY
            'last_days_date_rows' => $lastDaysDate,
        ]);

        $this->assertCount(20, $ranksData); // 20 элементом

        $previousScore = null;
        foreach ($ranksData as $rank) {
            if ($previousScore !== null) {
                $this->assertGreaterThanOrEqual($rank->total_score, $previousScore); // предыдущий score больше текущего
            }
            $previousScore = $rank->total_score;
        }

        $previousRank = null;
        foreach ($ranksData as $rank) {
            if ($previousRank !== null) {
                $this->assertLessThan($rank->rank, $previousRank); // предыдущий rank меньше текущего
            }
            $previousRank = $rank->rank;
        }

        foreach ($ranksData as $key => $rank) {

            if ($key <= 9) {
                $this->assertEquals('top_10', $rank->group);
            } else {
                $this->assertEquals('bottom_10', $rank->group);
            }

        }

        $timeExecution = (hrtime(true) - $time) / 1e+9 . ' sec';
        $memoryExecution = (memory_get_usage() - $memory) / 1024 / 1024 . 'mb';
        $memoryPeakExecution = (memory_get_peak_usage() - $memoryPeak) / 1024 / 1024 . 'mb';

        $console->writeln("task 1 - sql");
        $console->writeln("timeExecution: $timeExecution");
        $console->writeln("memoryExecution: $memoryExecution");
        $console->writeln("memoryPeakExecution: $memoryPeakExecution");
    }
}
