<?php

namespace App\Services;

use App\Models\Seller;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegisterService
{
    public function register(array $data): User
    {
        return DB::transaction(function () use ($data) {

            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            Seller::create([
                'user_id' => $user->id,

                // Seller / Company Profile
                'tin'             => $data['tin'] ?? null,
                'registered_name' => $data['registered_name'],
                'trade_name'      => $data['trade_name'] ?? null,
                'branch_code'     => $data['branch_code'] ?? null,

                // Address
                'address_line_1' => $data['address_line_1'],
                'address_line_2' => $data['address_line_2'] ?? null,
                'barangay'       => $data['barangay'] ?? null,
                'city'           => $data['city'] ?? null,
                'province'       => $data['province'] ?? null,
                'postal_code'    => $data['postal_code'] ?? null,
                'country_id'     => $data['country_id'],

                // Contact
                'company_email' => $data['company_email'] ?? null,
                'phone'         => $data['phone'] ?? null,
            ]);

            return $user;
        });
    }
}
