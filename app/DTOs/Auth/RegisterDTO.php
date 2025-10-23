<?php

namespace App\DTOs\Auth;

use Illuminate\Http\Request;

class RegisterDTO
{
    public function __construct(public string $name, public string $email, public string $password) {}

    public static function getFromRequest(Request $request): RegisterDTO
    {
        return new static(
            $request->name,
            $request->email,
            $request->password,
        );
    }
}
