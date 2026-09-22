<?php

namespace App\Services;

use App\Models\User;
use App\Models\Seller;
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

                //Seller/Company Profile Info
                'tin'             => $data['tin'],
                'registered_name' => $data['registered_name'],
                'trade_name'      => $data['trade_name'],
                'branch_code'     => $data['branch_code'],

                //Address
                'address_line_1'  => $data['address_line_1'],
                'address_line_2'  => $data['address_line_2'],
                'barangay'        => $data['barangay'],
                'city'            => $data['city'],
                'province'        => $data['province'],
                'postal_code'     => $data['postal_code'],
                'country_code'    => $data['country_code'],

                //Contact
                'company_email'   => $data['company_email'],
                'phone'           => $data['phone'],
            ]);

            return $user;
        });
    }
}
