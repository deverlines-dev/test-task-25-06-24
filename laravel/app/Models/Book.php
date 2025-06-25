<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\RowIdTrait;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $title
 * @property string $description
 *
 * @property int $pages_count
 *
 * @property Collection<UserBookmark> $bookmarks
 *
 */
class Book extends AbstractModel
{
    use RowIdTrait;

    protected function casts(): array
    {
        return [
            'rowid' => 'string',
            'title' => 'string',
            'description' => 'string',
            'pages_count' => 'int',
        ];
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPagesCount(): int
    {
        return $this->pages_count;
    }

    public function setPagesCount(int $pages_count): static
    {
        $this->pages_count = $pages_count;

        return $this;
    }

    /**
     * @return HasMany<UserBookmark>
     */
    public function bookmarks(): HasMany
    {
        return $this->hasMany(UserBookmark::class, 'book_id');
    }
}
