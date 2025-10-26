<?php

namespace App\Services\Pipes;

use App\Exceptions\LogicException;
use Closure;

class CheckSufficientBalancePipe extends AbstractPaymentCheckerPipe
{
    public function handle($request, Closure $next)
    {
        if ($request['wallet']->balance < $request['invoice']->amount) {
            throw new LogicException('Insufficient wallet balance.');
        }

        return $next($request);
    }
}
