<?php

namespace App\Services;

use App\Jobs\SendInvoicesEmailJob;
use App\Models\Buyer;
use App\Models\EmailJob;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;

class MailerService
{
    public function getBuyers(int $sellerId)
    {
        return Buyer::where('seller_id', $sellerId)
            ->select('id', 'tin', 'registered_name', 'email')
            ->orderBy('registered_name')
            ->get();
    }

    public function getInvoices(int $sellerId, int $buyerId)
    {
        return Invoice::where('seller_id', $sellerId)
            ->where('buyer_id', $buyerId)
            ->select('id', 'invoice_number', 'reference_number', 'invoice_date', 'due_date', 'total_amount', 'payment_status')
            ->latest('invoice_date')
            ->get();
    }

    public function sendInvoices(int $sellerId, int $buyerId, array $invoiceIds): void
    {
        $buyer = Buyer::where('seller_id', $sellerId)->findOrFail($buyerId);

        if (!$buyer->email) {
            abort(422, 'Buyer does not have an email address.');
        }

        $invoices = Invoice::where('seller_id', $sellerId)
            ->where('buyer_id', $buyerId)
            ->whereIn('id', $invoiceIds)
            ->get();

        if ($invoices->isEmpty()) {
            abort(422, 'No valid invoices selected.');
        }

        DB::transaction(function () use ($sellerId, $buyer, $invoices) {
            $emailJob = EmailJob::create([
                'seller_id' => $sellerId,
                'buyer_id' => $buyer->id,
                'email' => $buyer->email,
                'subject' => 'Your Invoice(s)',
                'status' => 'PENDING',
                'total_items' => $invoices->count(),
            ]);

            foreach ($invoices as $invoice) {
                $emailJob->items()->create([
                    'invoice_id' => $invoice->id,
                    'status' => 'PENDING',
                ]);
            }

            SendInvoicesEmailJob::dispatch($emailJob->id);
        });
    }

    public function sendInvoice(Invoice $invoice): void
    {
        $invoice->load(['seller', 'buyer']);

        $email = $invoice->buyer?->email ?: $invoice->buyer_email;

        if (!$email) {
            abort(422, 'Buyer does not have an email address.');
        }

        $buyerId = $invoice->buyer_id;

        DB::transaction(function () use ($invoice, $email, $buyerId) {
            $emailJob = EmailJob::create([
                'seller_id' => $invoice->seller_id,
                'buyer_id' => $buyerId,
                'email' => $email,
                'subject' => 'Your Invoice',
                'status' => 'PENDING',
                'total_items' => 1,
            ]);

            $emailJob->items()->create([
                'invoice_id' => $invoice->id,
                'status' => 'PENDING',
            ]);

            SendInvoicesEmailJob::dispatch($emailJob->id);
        });
    }
}
