<?php

namespace App\Http\Controllers\General;

use App\DTOs\Auth\LoginDTO;
use App\DTOs\Auth\RegisterDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginValidation;
use App\Http\Requests\RegisterUserValidation;
use App\Http\Resources\UserDetailsResource;
use App\Http\Resources\UserRegisterResource;
use App\Models\User;
use App\Services\Interfaces\AuthenticationServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function __construct(protected AuthenticationServiceInterface $service) {}

    public function register(RegisterUserValidation $request): UserRegisterResource
    {
        $response = $this->service->register(RegisterDTO::getFromRequest($request));

        return UserRegisterResource::make($response);
    }

    public function login(LoginValidation $request): JsonResponse
    {
        $response = $this->service->login(LoginDTO::getFromRequest($request));

        if ($response->failed()) {
            return response()->json(['error' => trans('auth.unauthorized')], Response::HTTP_UNAUTHORIZED);
        }

        return response()->json($response->json(), Response::HTTP_OK);
    }

    public function user(Request $request): UserDetailsResource
    {
        return UserDetailsResource::make($request->user());
    }

    public function logout(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $this->service->logout($user);

        return response()->json(['message' => trans('auth.logout')], Response::HTTP_OK);
    }
}
