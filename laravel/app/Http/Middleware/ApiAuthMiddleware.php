<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Предположим тут у нас происходит проверка токена + авторизация по токену (jwt)
 */
readonly class ApiAuthMiddleware
{
    public function __construct(private AuthManager $auth)
    {

    }

    public function handle(Request $request, Closure $next): Response
    {
        $user = new User()->newQuery()->orderByDesc('id')->first();

        $this->auth->setUser($user);

        return $next($request);
    }
}
