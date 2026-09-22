<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Services\ItemService;
use App\Http\Requests\Item\StoreItemRequest;
use App\Http\Requests\Item\UpdateItemRequest;
use Inertia\Inertia;

class ItemController extends Controller
{
    public function __construct(
        private ItemService $service
    ) {}

    public function index()
    {
        return Inertia::render('item/Index', [
            'items' => $this->service->getItems(
                request()->only('search')
            ),
            'filters' => request()->only('search'),
        ]);
    }

    public function create()
    {
        return Inertia::render('item/Create');
    }

    public function show(Item $item)
    {
        $this->service->authorizeItem($item);

        return Inertia::render('item/Show', [
            'item' => $item,
        ]);
    }

    public function store(StoreItemRequest $request)
    {
        $this->service->create($request->validated());

        return redirect()
            ->route('item.index')
            ->with('success', 'Item created successfully.');
    }

    public function edit(Item $item)
    {
        $this->service->authorizeItem($item);

        return Inertia::render('item/Edit', [
            'item' => $item,
        ]);
    }

    public function update(
        UpdateItemRequest $request,
        Item $item
    ) {
        $this->service->authorizeItem($item);

        $this->service->update(
            $item,
            $request->validated()
        );

        return redirect()
            ->route('item.index')
            ->with('success', 'Item updated successfully.');
    }

    public function destroy(Item $item)
    {
        $this->service->authorizeItem($item);

        $this->service->delete($item);

        return back()->with(
            'success',
            'Item deleted successfully.'
        );
    }
}
