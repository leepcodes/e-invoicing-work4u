<?php

namespace App\Services;

use App\Models\Seller;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class SellerService
{
    public function getSeller(int $userId): Seller
    {
        return Seller::where('user_id', $userId)->firstOrFail();
    }

    public function update(Seller $seller, array $data): Seller
    {
        if (isset($data['logo']) && $data['logo'] instanceof UploadedFile) {
            if ($seller->logo) {
                Storage::disk('public')->delete($seller->logo);
            }

            $data['logo'] = $data['logo']->store('seller-logos', 'public');
        }

        $seller->update($data);

        return $seller->fresh();
    }
}
