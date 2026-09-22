<?php

namespace App\Services;

use App\Models\Buyer;
use App\Models\Seller;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BuyerService
{
    public function getBuyers(array $filters = [])
    {
        $sellerId = auth()->user()?->seller?->id;

        return Buyer::query()
            ->where('seller_id', $sellerId)
            ->select([
                'id',
                'seller_id',
                'tin',
                'registered_name',
                'trade_name',
                'customer_code',
                'address_line_1',
                'barangay',
                'city',
                'province',
                'country_code',
                'email',
                'phone',
                'created_at',
            ])
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('customer_code', 'like', "%{$search}%")
                        ->orWhere('tin', 'like', "%{$search}%")
                        ->orWhere('registered_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();
    }

    public function create(Seller $seller, array $data): Buyer
    {
        return DB::transaction(function () use ($seller, $data) {

            return Buyer::create([
                //Seller Information
                'seller_id' => $seller->id,

                // Buyer Information
                'tin'             => $data['tin'],
                'registered_name' => $data['registered_name'],
                'trade_name'      => $data['trade_name'],
                'customer_code'   => $data['customer_code'],

                // Address
                'address_line_1' => $data['address_line_1'],
                'address_line_2' => $data['address_line_2'],
                'barangay'       => $data['barangay'],
                'city'           => $data['city'],
                'province'       => $data['province'],
                'postal_code'    => $data['postal_code'],
                'country_code'   => $data['country_code'],

                // Contact
                'email' => $data['email'],
                'phone' => $data['phone'],
            ]);
        });
    }

    public function update(Buyer $buyer, array $data): Buyer
    {
        return DB::transaction(function () use ($buyer, $data) {

            $buyer->update([
                'tin'             => $data['tin'],
                'registered_name' => $data['registered_name'],
                'trade_name'      => $data['trade_name'],
                'customer_code'   => $data['customer_code'],

                'address_line_1' => $data['address_line_1'],
                'address_line_2' => $data['address_line_2'],
                'barangay'       => $data['barangay'],
                'city'           => $data['city'],
                'province'       => $data['province'],
                'postal_code'    => $data['postal_code'],
                'country_code'   => $data['country_code'],

                'email' => $data['email'],
                'phone' => $data['phone'],
            ]);

            return $buyer->fresh();
        });
    }
    public function delete(Buyer $buyer): void
    {
        DB::transaction(function () use ($buyer) {
            $buyer->invoices()->delete();
            $buyer->delete();
        });
    }
}
