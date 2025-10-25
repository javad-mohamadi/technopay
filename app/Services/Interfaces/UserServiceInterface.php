<?php

namespace App\Services\Interfaces;

interface UserServiceInterface
{
    public function findOrFail(int $id);

    public function findAndLock(int $id);
}
