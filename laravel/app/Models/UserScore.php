<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\RowIdTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $score
 *
 * @property User $user
 */
class UserScore extends AbstractModel
{
    use RowIdTrait;

    public function getScore(): int
    {
        return $this->score;
    }

    public function setScore(int $score): static
    {
        $this->score = $score;

        return $this;
    }

    /**
     * @return BelongsTo<User>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    protected function casts(): array
    {
        return [
            'row_id' => 'string',
            'score' => 'int',
        ];
    }
}
