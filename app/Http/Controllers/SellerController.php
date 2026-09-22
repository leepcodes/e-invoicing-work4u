<?php

namespace App\Http\Controllers;

use App\Http\Requests\Seller\UpdateSellerRequest;
use App\Services\SellerService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class SellerController extends Controller
{
    public function __construct(private SellerService $sellerService) {}

    public function index(): Response
    {
        return Inertia::render('seller/Index', [
            'seller' => $this->sellerService->getSeller(auth()->id()),
        ]);
    }

    public function edit(): Response
    {
        return Inertia::render('seller/Edit', [
            'seller' => $this->sellerService->getSeller(auth()->id()),
        ]);
    }

    public function update(UpdateSellerRequest $request): RedirectResponse
    {
        $seller = $this->sellerService->getSeller(auth()->id());
        $this->sellerService->update($seller, $request->validated());

        return redirect()
        ->route('seller.index')
        ->with('success', 'Seller information updated successfully.');
    }
}
