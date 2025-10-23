<?php

namespace App\Services;

use App\DTOs\Auth\RegisterDTO;
use App\Models\User;
use App\Services\Interfaces\AuthenticationServiceInterface;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response as HttpResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class AuthenticationService implements AuthenticationServiceInterface
{
    /**
     * @string
     */
    const AUTH_ROUTE = '/oauth/token';

    /**
     * @throws ConnectionException
     */
    public function login($dto): PromiseInterface|HttpResponse
    {
        return $this->createToken(
            email: $dto->email,
            password: $dto->password,
        );
    }

    /**
     * @throws ConnectionException
     */
    public function register(RegisterDTO $dto): PromiseInterface|HttpResponse
    {
        User::query()->create([
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => Hash::make($dto->password),
        ]);

        return $this->createToken(
            email: $dto->email,
            password: $dto->password,
        );
    }

    /**
     * @throws ConnectionException
     */
    private function createToken(string $email, string $password): PromiseInterface|HttpResponse
    {
        return Http::asForm()->post(config('app.url').self::AUTH_ROUTE, [
            'grant_type' => 'password',
            'client_id' => config('auth.clients.id'),
            'client_secret' => config('auth.clients.secret'),
            'username' => $email,
            'password' => $password,
            'scope' => '',
        ]);
    }
}
