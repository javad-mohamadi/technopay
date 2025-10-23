<?php

namespace App\Http\Controllers\General\V1;

use App\DTOs\Auth\LoginDTO;
use App\DTOs\Auth\RegisterDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\Interfaces\AuthenticationServiceInterface;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function __construct(protected AuthenticationServiceInterface $service) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $response = $this->service->register(RegisterDTO::getFromRequest($request));

        if ($response->failed()) {
            return response()->json(['error' => 'Bad Request'], Response::HTTP_BAD_REQUEST);
        }

        return response()->json($response->json(), Response::HTTP_CREATED);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $response = $this->service->login(LoginDTO::getFromRequest($request));

        if ($response->failed()) {
            return response()->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        return response()->json($response->json());
    }
}
