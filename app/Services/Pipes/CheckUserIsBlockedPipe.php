<?php

namespace App\Services\Pipes;

use App\Exceptions\LogicException;
use Closure;

class CheckUserIsBlockedPipe
{
    public function handle($request, Closure $next)
    {
        if ($request['user']->is_blocked) {
            throw new LogicException('User is blocked.');
        }

        return $next($request);
    }
}
