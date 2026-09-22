<?php

namespace App\Services;

use App\Models\Item;

class ItemService
{
    public function getItems(array $filters = [])
    {
        $sellerId = auth()->user()?->seller?->id;

        return Item::query()
            ->where('seller_id', $sellerId)
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('item_code', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('unit_code', 'like', "%{$search}%");
                });
            })
            ->orderBy('item_code')
            ->paginate(10)
            ->withQueryString();
    }

    public function create(array $data): Item
    {
        return Item::create([
            'seller_id' => auth()->user()->seller->id,
            'item_code' => $data['item_code'],
            'description' => $data['description'],
            'unit_code' => $data['unit_code'],
            'unit_price' => $data['unit_price'],
        ]);
    }

    public function update(Item $item, array $data): Item
    {
        $item->update([
            'item_code' => $data['item_code'],
            'description' => $data['description'],
            'unit_code' => $data['unit_code'],
            'unit_price' => $data['unit_price'],
        ]);

        return $item;
    }

    public function delete(Item $item): void
    {
        $item->delete();
    }
    public function authorizeItem(Item $item): void
    {
        $sellerId = auth()->user()?->seller?->id;

        abort_unless(
            $item->seller_id === $sellerId,
            403
        );
    }

}
