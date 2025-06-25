<?php

namespace App\Http\Controllers\Api\User\UserBookmarks\Responses;

use App\Http\Controllers\Api\Responses\Pagination\PaginationResponseData;
use Spatie\LaravelData\Data;

class UserBookmarksListJsonResponse extends Data
{
    public array $items = [];
    public PaginationResponseData $pagination;

    public function addItem(UserBookmarksListItemData $item): static
    {
        $this->items[] = $item;

        return $this;
    }

    public function setPagination(PaginationResponseData $pagination): static
    {
        $this->pagination = $pagination;

        return $this;
    }
}
