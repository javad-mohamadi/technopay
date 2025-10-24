<?php

namespace App\Services;

use App\DTOs\Auth\RegisterDTO;
use App\Exceptions\LogicException;
use App\Models\User;
use App\Services\Interfaces\AuthenticationServiceInterface;
use App\Services\Interfaces\WalletServiceInterface;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response as HttpResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AuthenticationService implements AuthenticationServiceInterface
{
    public function __construct(protected WalletServiceInterface $walletService) {}

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
        DB::beginTransaction();

        try {
            $user = User::query()->create([
                'name' => $dto->name,
                'email' => $dto->email,
                'password' => Hash::make($dto->password),
            ]);
            $this->walletService->create($user, config('wallet.initial_balance'));

            DB::commit();

            return $this->createToken(
                email: $user->email,
                password: $dto->password
            );
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('The transaction was rolled back and the user was not created: '.$e->getMessage());
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
