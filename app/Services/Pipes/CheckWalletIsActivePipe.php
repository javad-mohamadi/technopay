<?php

namespace App\Services\Pipes;

use App\Exceptions\LogicException;
use Closure;

class CheckWalletIsActivePipe
{
    public function handle($request, Closure $next)
    {
        if (! $request['wallet']->is_active) {
            throw new LogicException('Wallet is not active.');
        }

        return $next($request);
    }
}
