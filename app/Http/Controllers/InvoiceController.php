<?php

namespace App\Http\Controllers;

use App\Http\Requests\Invoice\CreateInvoiceRequest;
use App\Http\Requests\Invoice\UpdateInvoiceRequest;
use App\Models\Invoice;
use App\Services\InvoiceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Barryvdh\DomPDF\Facade\Pdf;


class InvoiceController extends Controller
{
    public function index(Request $request, InvoiceService $invoiceService): Response
    {
        $filters = $request->only([
            'search', 'status', 'invoice_date_from', 'invoice_date_to', 'due_date_from', 'due_date_to',
        ]);

        return Inertia::render('invoice/Index', [
            'invoices' => $invoiceService->getInvoices($filters),
            'filters' => $filters,
        ]);
    }

    public function show(Invoice $invoice, InvoiceService $invoiceService): Response
    {
        return Inertia::render('invoice/Show', [
            'invoice' => $invoiceService->getInvoiceDetails($invoice),
        ]);
    }

    public function edit(Invoice $invoice): Response
    {
        return Inertia::render('invoice/Edit', [
            'invoice' => $invoice->load('buyer:id,registered_name,trade_name,customer_code'),
        ]);
    }

    public function create(InvoiceService $invoiceService): Response
    {
        return Inertia::render('invoice/Create', $invoiceService->getCreateData());
    }

    public function store(CreateInvoiceRequest $request, InvoiceService $invoiceService): RedirectResponse
    {
        $invoiceService->create($request->validated());

        return redirect()->route('invoice.index')->with('success', 'Invoice created successfully.');
    }

    public function update(UpdateInvoiceRequest $request, Invoice $invoice, InvoiceService $invoiceService): RedirectResponse
    {
        $invoiceService->updatePayment($invoice, $request->validated());

        return redirect()->route('invoice.index')->with('success', 'Invoice updated successfully.');
    }
    public function destroy(Invoice $invoice): RedirectResponse
    {
        abort_unless($invoice->seller_id === auth()->user()->seller->id, 403);
        $invoice->delete();
        return redirect()->route('invoice.index')->with('success', 'Invoice deleted successfully.');
    }

    public function viewPdf(Invoice $invoice)
    {
        $data = $this->getPdfData($invoice);

        $pdf = Pdf::loadView('invoices.pdf', $data)
            ->setPaper('a4', 'portrait')
            ->setOption('enable-local-file-access', true);

        return $pdf->stream("invoice-{$invoice->invoice_number}.pdf");
    }

    public function downloadPdf(Invoice $invoice)
    {
        $data = $this->getPdfData($invoice);

        $pdf = Pdf::loadView('invoices.pdf', $data)
            ->setPaper('letter')
            ->setOption('isRemoteEnabled', false)
            ->setOption('margin-top', 0.5)
            ->setOption('margin-bottom', 0.5)
            ->setOption('margin-left', 1)
            ->setOption('margin-right', 1)
            ->setOption('dpi', 96)
            ->setOption('font-subsetting-enabled', true)
            ->setOption('enable-local-file-access', true)
            ->setOption('font-subsetting-enabled', true);

        return $pdf->download("Invoice-{$invoice->invoice_number}.pdf");
    }

    public function getPdfData(Invoice $invoice): array
    {
        $invoice->loadMissing(['seller', 'buyer', 'items']);

        $initials = collect(explode(' ', trim($invoice->seller->registered_name ?? '')))
            ->filter()
            ->map(fn ($name) => strtoupper(substr($name, 0, 1)))
            ->take(2)
            ->join('');

        return [
            'invoice' => $invoice,
            'initials' => $initials,
        ];
    }

    public function json(Invoice $invoice, InvoiceService $invoiceService)
    {
        abort_unless($invoice->seller_id === auth()->user()?->seller?->id, 403);

        return response()->json($invoiceService->toJson($invoice), 200, [], JSON_PRETTY_PRINT);
    }
}
