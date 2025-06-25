<?php

namespace App\Http\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

readonly class UserRepository
{
    /**
     * @return Builder<User>
     */
    public function query(): Builder
    {
        return new User()->newQuery();
    }


}
