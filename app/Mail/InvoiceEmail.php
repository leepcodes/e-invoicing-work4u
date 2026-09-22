<?php

namespace App\Mail;

use App\Models\EmailJob;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public EmailJob $emailJob,
        public array $invoiceAttachments
    ) {}

    public function build()
    {
        $this->emailJob->load([
            'seller',
            'items.invoice.buyer',
        ]);

        $seller = $this->emailJob->seller;

        $companyName = $seller?->trade_name
            ?: $seller?->registered_name
            ?: 'Your Company';

        $invoice = $this->emailJob->items->first()?->invoice;

        $buyerName = $invoice?->buyer?->registered_name
            ?: $invoice?->buyer_name
            ?: 'Customer';

        $initials = collect(
            preg_split('/\s+/', trim($companyName))
        )
            ->filter()
            ->take(2)
            ->map(fn ($word) => strtoupper(substr($word, 0, 1)))
            ->implode('');

        $logoUrl = null;

        if ($seller?->logo) {
            $logoUrl = asset('storage/' . ltrim($seller->logo, '/'));
        }

        $mail = $this
            ->subject($this->emailJob->subject ?? 'Your Invoice')
            ->view('emails.invoice', [
                'emailJob' => $this->emailJob,
                'invoiceAttachments' => $this->invoiceAttachments,
                'companyName' => $companyName,
                'buyerName' => $buyerName,
                'seller' => $seller,
                'invoice' => $invoice,
                'initials' => $initials,
                'logoUrl' => $logoUrl,
            ]);

        foreach ($this->invoiceAttachments as $attachment) {
            $mail->attachData(
                $attachment['data'],
                $attachment['name'],
                [
                    'mime' => 'application/pdf',
                ]
            );
        }

        return $mail;
    }
}
