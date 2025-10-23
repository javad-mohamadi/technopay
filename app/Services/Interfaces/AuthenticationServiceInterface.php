<?php

namespace App\Services\Interfaces;

use App\DTOs\Auth\LoginDTO;
use App\DTOs\Auth\RegisterDTO;

interface AuthenticationServiceInterface
{
    public function login(LoginDTO $dto);

    public function register(RegisterDTO $dto);
}
