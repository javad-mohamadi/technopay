<?php

namespace App\Services;

use App\DTOs\Auth\RegisterDTO;
use App\Models\User;
use App\Services\Interfaces\AuthenticationServiceInterface;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response as HttpResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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

    public function register(RegisterDTO $dto): PromiseInterface|HttpResponse
    {
        $user = User::query()->create([
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => Hash::make($dto->password),
        ]);

        return $this->createToken(
            email: $user->email,
            password: $dto->password
        );
    }

    public function logout(User $user): void
    {
        try {
            $accessToken = $user->token();

            if ($accessToken) {
                $accessToken->revoke();

                DB::table('oauth_refresh_tokens')
                    ->where('access_token_id', $accessToken->id)
                    ->update(['revoked' => true]);
            }
        } catch (\Throwable $e) {
            Log::error('Logout failed: '.$e->getMessage());
        }
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
