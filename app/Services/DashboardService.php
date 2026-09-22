<?php

namespace App\Services;

use App\Models\Buyer;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;

class DashboardService
{
    public function index(): array
    {
        $sellerId = Auth::user()->seller->id;

        $invoiceQuery = Invoice::where('seller_id', $sellerId);

        return [
            'buyerCount' => Buyer::where('seller_id', $sellerId)->count(),

            'invoiceCount' => (clone $invoiceQuery)->count(),

            'paidInvoiceCount' => (clone $invoiceQuery)
                ->where('payment_status', 'paid')
                ->count(),

            'unpaidInvoiceCount' => (clone $invoiceQuery)
                ->where('payment_status', 'unpaid')
                ->count(),

            'partiallyPaidInvoiceCount' => (clone $invoiceQuery)
                ->where('payment_status', 'partially paid')
                ->count(),
        ];
    }
}
