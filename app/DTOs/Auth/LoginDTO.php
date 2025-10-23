<?php

namespace App\DTOs\Auth;

use Illuminate\Http\Request;

class LoginDTO
{
    public function __construct(public string $email, public string $password) {}

    public static function getFromRequest(Request $request): LoginDTO
    {
        return new static(
            $request->email,
            $request->password,
        );
    }
}
