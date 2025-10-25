<?php

namespace App\Services\Pipes;

use Closure;

abstract class AbstractPaymentCheckerPipe
{
    abstract public function handle($request, Closure $next);
}
