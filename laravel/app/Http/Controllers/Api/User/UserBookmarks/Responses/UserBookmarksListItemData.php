<?php

namespace App\Http\Controllers\Api\User\UserBookmarks\Responses;

use App\Http\Controllers\Api\Book\Responses\BookPreviewItemData;
use Spatie\LaravelData\Data;

class UserBookmarksListItemData extends Data
{
    public function __construct(public int $id, public string $bookmark, public BookPreviewItemData $book)
    {

    }
}
