<?php

namespace App\Services\Pipes;

use App\Exceptions\LogicException;
use Closure;

class CheckGlobalDailyLimitPipe extends AbstractPaymentCheckerPipe
{
    public function handle($request, Closure $next)
    {
        $currentSpend = $request['globalSpend'] ? $request['globalSpend'] : 0;
        $limit = config('wallet.max_global_daily_spend');

        if (($currentSpend + $request['invoice']->amount) > $limit) {
            throw new LogicException('Global daily spending limit exceeded');
        }

        return $next($request);
    }
}
