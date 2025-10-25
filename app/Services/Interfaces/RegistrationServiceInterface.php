<?php

namespace App\Services\Interfaces;

use App\DTOs\Auth\RegisterDTO;
use App\Models\User;

interface RegistrationServiceInterface
{
    public function register(RegisterDTO $dto): User;
}
