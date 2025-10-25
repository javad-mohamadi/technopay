<?php

namespace App\Services;

use App\Repositories\User\UserRepositoryInterface;
use App\Services\Interfaces\UserServiceInterface;

class UserService implements UserServiceInterface
{
    public function __construct(protected UserRepositoryInterface $userRepository) {}

    public function findOrFail(int $id)
    {
        return $this->userRepository->findOrFail($id);
    }

    public function findAndLock(int $id)
    {
        return $this->userRepository->findAndLock($id);
    }
}
