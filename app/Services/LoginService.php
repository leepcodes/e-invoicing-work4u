<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;



class LoginService
{
    public function login(array $data): User
    {
    if (!Auth::attempt($data)) {
                throw ValidationException::withMessages([
                    'email' => 'Invalid credentials.',
                ]);
            }
            request()->session()->regenerate();
            return Auth::user();
        }
}
