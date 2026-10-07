<?php

namespace App\Http\Controllers\Import;

use App\Http\Controllers\Controller;
use App\Models\CsvImportJob;
use App\Services\InvoiceImportService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InvoiceImportController extends Controller
{
    public function create()
    {
        $sellerId = auth()->user()?->seller?->id;

        $recentJobs = CsvImportJob::query()
            ->where('seller_id', $sellerId)
            ->latest()
            ->limit(8)
            ->get();

        return Inertia::render('invoice/Import', [
            'importJob' => $recentJobs->first(),
            'recentJobs' => $recentJobs,
        ]);
    }

    public function store(
        Request $request,
        InvoiceImportService $invoiceImportService
    ) {
        $invoiceImportService->import($request);

        return redirect()
            ->route('invoice.import')
            ->with('success', 'CSV import started.');
    }

    public function show(CsvImportJob $importJob)
    {
        abort_unless(
            $importJob->seller_id === auth()->user()?->seller?->id,
            403
        );

        return response()->json($importJob);
    }

    public function downloadCsvTemplate()
    {
        $path = storage_path('app/templates/invoice_import_template.csv');

        abort_unless(file_exists($path), 404, 'CSV template not found.');

        return response()->download(
            $path,
            'invoice_import_template.csv',
            ['Content-Type' => 'text/csv']
        );
    }

   public function downloadXlsxTemplate()
    {
        $path = storage_path('app/templates/invoice_import_template.xlsx');

        abort_unless(
            is_file($path),
            404,
            'Excel template not found.'
        );

        return response()->streamDownload(
            function () use ($path) {
                readfile($path);
            },
            'invoice_import_template.xlsx',
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Length' => filesize($path),
                'Content-Disposition' => 'attachment; filename="invoice_import_template.xlsx"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]
        );
    }
}
