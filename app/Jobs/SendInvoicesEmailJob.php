<?php

namespace App\Jobs;

use App\Mail\InvoiceEmail;
use App\Models\EmailJob;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendInvoicesEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $emailJobId) {}

    public function handle(): void
    {
        $emailJob = EmailJob::with(['buyer', 'items.invoice'])->findOrFail($this->emailJobId);
        $emailJob->update(['status' => 'PROCESSING']);

        try {
            $attachments = [];

            foreach ($emailJob->items as $item) {
                $invoice = $item->invoice;
                $invoice->load(['seller', 'buyer', 'items']);

                $item->update(['status' => 'PROCESSING']);

                $initials = collect(explode(' ', trim($invoice->seller->registered_name ?? '')))
                    ->filter()
                    ->map(fn ($name) => strtoupper(substr($name, 0, 1)))
                    ->take(2)
                    ->join('');

                $pdf = Pdf::loadView('invoices.pdf', [
                    'invoice' => $invoice,
                    'initials' => $initials,
                ])
                    ->setPaper('a4', 'portrait')
                    ->setOption('isRemoteEnabled', false)
                    ->setOption('enable-local-file-access', true)
                    ->setOption('dpi', 96)
                    ->setOption('font-subsetting-enabled', true);

                $attachments[] = [
                    'data' => $pdf->output(),
                    'name' => "Invoice-{$invoice->invoice_number}.pdf",
                ];
            }

            Mail::to($emailJob->email)->send(
                new InvoiceEmail($emailJob, $attachments)
            );

            $emailJob->items()->update([
                'status' => 'SENT',
                'sent_at' => now(),
            ]);

            $emailJob->update([
                'status' => 'SENT',
                'sent_items' => $emailJob->items()->count(),
                'sent_at' => now(),
            ]);
        } catch (\Throwable $e) {
            $emailJob->items()->update([
                'status' => 'FAILED',
                'error_message' => $e->getMessage(),
            ]);

            $emailJob->update([
                'status' => 'FAILED',
                'error_message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
