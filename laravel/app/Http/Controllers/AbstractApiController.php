<?php

namespace App\Http\Controllers;

use App\Models\User;

readonly abstract class AbstractApiController
{
    /**
     * @noinspection PhpIncompatibleReturnTypeInspection
     */
    protected function getAuthUser(): ?User
    {
        return auth()->user();
    }
}
