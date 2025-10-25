<?php

namespace App\Services;

use App\DTOs\Auth\RegisterDTO;
use App\Exceptions\LogicException;
use App\Models\User;
use App\Services\Interfaces\AuthenticationServiceInterface;
use App\Services\Interfaces\RegistrationServiceInterface;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response as HttpResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AuthenticationService implements AuthenticationServiceInterface
{
    public function __construct(protected RegistrationServiceInterface $registrationService) {}

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

    public function register(RegisterDTO $dto): array
    {
        try {
            $user = $this->registrationService->register($dto);

            $tokenData = $this->createToken(
                email: $user->email,
                password: $dto->password
            );
            if ($tokenData->failed()) {
                throw new \Exception(trans('auth.bad_request'), Response::HTTP_BAD_REQUEST);
            }

            return [
                'user' => $user,
                'tokenData' => $tokenData,
            ];
        } catch (Throwable $e) {
            Log::error('User was not created: '.$e->getMessage());
            throw new LogicException(Response::HTTP_BAD_REQUEST, 'User was not created.');
        }
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
        } catch (Throwable $e) {
            Log::error('Logout failed: '.$e->getMessage());
            throw new LogicException(Response::HTTP_BAD_REQUEST, 'Logout failed');
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
