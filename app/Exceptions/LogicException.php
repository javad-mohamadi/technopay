<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class LogicException extends Exception
{
    protected array $error;

    public function __construct(
        int $httpCode = ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,
        string $message = '',
        array $error = []
    ) {
        $message = empty($message) ? ResponseAlias::$statusTexts[$httpCode] : $message;
        $this->error = $error;
        parent::__construct($message, $httpCode);
    }

    public function getError(): array
    {
        return $this->error;
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
            'error' => $this->error,
        ], $this->getCode());
    }
}
