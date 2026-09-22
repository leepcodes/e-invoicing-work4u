<?php

use App\Http\Controllers\Api\InvoiceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    Route::get('/test-auth', function (Request $request) {
        return response()->json([
            'authenticated' => true,
            'user' => $request->user(),
        ]);
    });

    Route::post('/invoices', [InvoiceController::class, 'store']);
});
