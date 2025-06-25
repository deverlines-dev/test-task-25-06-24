<?php

namespace App\Http\Repositories;

use App\Models\UserBookmark;
use Illuminate\Database\Eloquent\Builder;

readonly class UserBookmarksRepository
{
    /**
     * @return Builder<UserBookmark>
     */
    public function query(): Builder
    {
        return new UserBookmark()->newQuery();
    }


}
