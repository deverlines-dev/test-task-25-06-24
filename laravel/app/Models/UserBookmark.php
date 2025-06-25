<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\RowIdTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $bookmark
 *
 * @property User $user
 * @property Book $book
 */
class UserBookmark extends AbstractModel
{
    use RowIdTrait;

    protected function casts(): array
    {
        return [
            'row_id' => 'string',
            'bookmark' => 'string',
        ];
    }

    public function getBookmark(): string
    {
        return $this->bookmark;
    }

    public function setBookmark(string $bookmark): static
    {
        $this->bookmark = $bookmark;

        return $this;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class, 'book_id');
    }
}
