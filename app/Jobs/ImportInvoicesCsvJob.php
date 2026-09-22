<?php

namespace App\Jobs;

use App\Models\CsvImportJob;
use App\Services\InvoiceImportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class ImportInvoicesCsvJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300;
    public int $tries = 1;

    public function __construct(
        public int $importJobId,
        public int $offset = 0,
        public int $sellerId = 0,
        public int $userId = 0,
    ) {}

    public function handle(InvoiceImportService $invoiceImportService): void
    {
        $importJob = CsvImportJob::findOrFail($this->importJobId);
        $sellerId = $importJob->seller_id;

        if (! $sellerId) {
            $importJob->update([
                'status' => 'FAILED',
                'error_message' => 'Seller account could not be determined for this import job.',
                'completed_at' => now(),
            ]);
            return;
        }

        $userId = $this->userId;

        if (! $userId) {
            $importJob->update([
                'status' => 'FAILED',
                'error_message' => 'User account could not be determined for this import job.',
                'completed_at' => now(),
            ]);
            return;
        }

        if ($this->offset === 0) {
            $importJob->update([
                'status' => 'PROCESSING',
                'started_at' => now(),
            ]);
        }

        try {
            $hasMoreRows = $invoiceImportService->processChunk(
                $importJob,
                $this->offset,
                500,
                $sellerId,
                $userId
            );

            if ($hasMoreRows) {
                self::dispatch(
                    $this->importJobId,
                    $this->offset + 500,
                    $sellerId,
                    $userId
                );
                return;
            }

            $importJob->refresh();
            $importJob->update([
                'status' => 'COMPLETED',
                'completed_at' => now(),
            ]);
        } catch (Throwable $e) {
            $importJob->update([
                'status' => 'FAILED',
                'error_message' => $e->getMessage(),
                'completed_at' => now(),
            ]);

            throw $e;
        }
    }
}
