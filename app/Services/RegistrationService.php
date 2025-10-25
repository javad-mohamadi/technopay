<?php

namespace App\Services;

use App\DTOs\Auth\RegisterDTO;
use App\Models\User;
use App\Repositories\User\UserRepositoryInterface;
use App\Services\Interfaces\RegistrationServiceInterface;
use App\Services\Interfaces\WalletServiceInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegistrationService implements RegistrationServiceInterface
{
    public function __construct(
        protected UserRepositoryInterface $userRepository,
        protected WalletServiceInterface $walletService
    ) {}

    public function register(RegisterDTO $dto): User
    {
        return DB::transaction(function () use ($dto) {
            $user = $this->userRepository->create([
                'name' => $dto->name,
                'email' => $dto->email,
                'password' => Hash::make($dto->password),
            ]);
            $initialBalance = config('wallet.initial_balance');
            $this->walletService->createForUser($user, $initialBalance);

            return $user->load('wallet');
        });
    }
}
