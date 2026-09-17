<?php

namespace App\Http\Middleware;

use App\Exceptions\Auth\UserBannedException;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsNotBanned
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user?->is_banned) {
            throw new UserBannedException($user->ban_reason);
        }

        return $next($request);
    }
}