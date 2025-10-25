<?php

namespace App\Services\Interfaces;

interface UserServiceInterface
{
    public function findAndLock(int $id);
}
