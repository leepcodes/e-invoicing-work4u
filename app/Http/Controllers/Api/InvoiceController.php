<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreInvoiceRequest;
use App\Services\InvoiceService;

class InvoiceController extends Controller
{
    public function __construct(private InvoiceService $invoiceService) {}

    public function store(StoreInvoiceRequest $request)
    {
        $invoice = $this->invoiceService->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Invoice created successfully.',
            'data' => $this->invoiceService->toJson($invoice),
        ], 201);
    }
}
