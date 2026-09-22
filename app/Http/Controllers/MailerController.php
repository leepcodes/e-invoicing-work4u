<?php

namespace App\Http\Controllers;

use App\Models\EmailJob;
use App\Models\Invoice;
use App\Services\MailerService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MailerController extends Controller
{
    public function index(Request $request, MailerService $service): Response
    {
        $sellerId = auth()->user()->seller->id;
        $buyerId = $request->integer('buyer_id');

        return Inertia::render('mailer/Index', [
            'buyers' => $service->getBuyers($sellerId),
            'invoices' => $buyerId ? $service->getInvoices($sellerId, $buyerId) : [],
            'selectedBuyerId' => $buyerId ?: null,
        ]);
    }

    public function send(Request $request, MailerService $service)
    {
        $data = $request->validate([
            'buyer_id' => ['required', 'integer'],
            'invoice_ids' => ['required', 'array', 'min:1'],
            'invoice_ids.*' => ['integer'],
        ]);

        $service->sendInvoices(
            auth()->user()->seller->id,
            $data['buyer_id'],
            $data['invoice_ids']
        );

        return back()->with('success', 'Invoices queued successfully.');
    }
    public function jobs()
    {
        $user = auth()->user();

        $sellerId = $user->seller?->id;

        if (!$sellerId) {
            abort(403, 'Seller account not found.');
        }

        $emailJobs = EmailJob::query()
            ->where('seller_id', $sellerId)
            ->with('buyer')
            ->latest()
            ->paginate(10);

        return Inertia::render('mailer/Jobs', [
            'emailJobs' => $emailJobs,
        ]);
    }

    public function show(EmailJob $emailJob): Response
    {
        $sellerId = auth()->user()->seller->id;

        $emailJob->load(['buyer', 'items.invoice.buyer']);

        abort_unless($emailJob->seller_id === $sellerId, 403);

        return Inertia::render('mailer/Show', [
            'emailJob' => [
                'id' => $emailJob->id,
                'email' => $emailJob->email,
                'subject' => $emailJob->subject,
                'status' => $emailJob->status,
                'sent_items' => $emailJob->sent_items,
                'created_at' => $emailJob->created_at?->format('M d, Y H:i'),
                'items' => $emailJob->items->map(fn ($item) => [
                    'id' => $item->id,
                    'invoice_id' => $item->invoice_id,
                    'buyer_name' => $item->invoice?->buyer?->registered_name ?? '-',
                    'status' => $item->status,
                    'sent_at' => $item->sent_at?->format('M d, Y H:i'),
                ])->values(),
            ],
        ]);
    }

    public function sendInvoice(Invoice $invoice, MailerService $service)
    {
        abort_unless($invoice->seller_id === auth()->user()->seller->id, 403);

        $service->sendInvoice($invoice);

        return back()->with('success', 'Invoice queued for email.');
    }
}
