<?php

namespace App\Http\Controllers;

use App\Http\Requests\Buyer\CreateBuyerRequest;
use App\Http\Requests\Buyer\UpdateBuyerRequest;
use App\Models\Buyer;
use App\Services\BuyerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BuyerController extends Controller
{
    public function index(Request $request, BuyerService $buyerService): Response
    {
        $filters = $request->only(['search']);

        return Inertia::render('buyer/Index', [
            'buyers' => $buyerService->getBuyers($filters),
            'filters' => $filters,
        ]);
    }

    public function show(Buyer $buyer)
    {
        $this->authorizeBuyer($buyer);

        return Inertia::render('buyer/Show', ['buyer' => $buyer]);
    }

    public function create()
    {
        return Inertia::render('buyer/Create');
    }

    public function edit(Buyer $buyer)
    {
        $this->authorizeBuyer($buyer);

        return Inertia::render('buyer/Edit', ['buyer' => $buyer]);
    }

    public function store(CreateBuyerRequest $request, BuyerService $buyerService): RedirectResponse
    {
        $seller = auth()->user()->seller;
        $buyerService->create($seller, $request->validated());

        return redirect()->route('buyer.index')->with('success', 'Buyer created successfully.');
    }

    public function update(UpdateBuyerRequest $request, Buyer $buyer, BuyerService $buyerService): RedirectResponse
    {
        $this->authorizeBuyer($buyer);
        $buyerService->update($buyer, $request->validated());

        return redirect()->route('buyer.index')->with('success', 'Buyer updated successfully.');
    }

    public function destroy(Buyer $buyer, BuyerService $buyerService): RedirectResponse
    {
        $this->authorizeBuyer($buyer);
        $buyerService->delete($buyer);

        return redirect()->route('buyer.index')->with('success', 'Buyer deleted successfully.');
    }

    private function authorizeBuyer(Buyer $buyer): void
    {
        abort_unless($buyer->seller_id === auth()->user()->seller->id, 403);
    }
}
