<?php

declare(strict_types=1);

namespace App\Data;

readonly class TopScopeData
{
    public function __construct(public int $userId, public int $totalScore, public int $rank)
    {

    }
}
