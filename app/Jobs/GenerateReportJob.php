<?php

namespace App\Jobs;

use App\Enums\Report\ReportStatus;
use App\Helpers\ArrayReportExport;
use App\Repositories\Report\ReportRepositoryInterface;
use App\Services\Report\ReportDataService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use RuntimeException;
use Throwable;

class GenerateReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $reportExportId,
        public array $data
    ) {
        //
    }

    public function handle(
        ReportDataService $reportDataService,
        ReportRepositoryInterface $reportRepository,
    ): void {
        $reportRepository->updateReport(
            $this->reportExportId,
            ReportStatus::PROCESSING
        );

        $disk = config('reports.disk', 'reports');

        $fileName = $this->data['type'] . '_' . $this->data['date_from'] . '_' . $this->data['date_to'] . '_'
            . Str::uuid() . '.xlsx';

        try {
            $rows = $this->loadAllRows(
                $reportDataService,
                $this->data['type'],
                $this->data['date_from'],
                $this->data['date_to']
            );

            $report = Excel::store(
                new ArrayReportExport($this->data['type'], $rows),
                $fileName,
                $disk
            );

            if (!$report) {
                throw new RuntimeException('Отчет не создан');
            }

            if (!Storage::disk($disk)->exists($fileName)) {
                throw new RuntimeException('Нет такого файла в хранилище: ' . $fileName);
            }

            $reportRepository->updateReport(
                $this->reportExportId,
                ReportStatus::FINISHED,
                $fileName
            );
        } catch (Throwable $e) {
            $reportRepository->updateReport(
                $this->reportExportId,
                ReportStatus::FAILED,
                null,
                $e->getMessage()
            );

            throw $e;
        }
    }

    private function loadAllRows(ReportDataService $reportDataService, string $type, string $from, string $to): array
    {
        $perPage = 1000;
        $page = 1;
        $allRows = [];

        while (true) {
            $payload = match ($type) {
                'rent_histories'  => $reportDataService->rentHistories($from, $to, $page, $perPage),
                'rental_requests' => $reportDataService->rentalRequests($from, $to, $page, $perPage),
                default => throw new \InvalidArgumentException("Unknown report type: {$type}"),
            };

            foreach (($payload['data'] ?? []) as $row) {
                $allRows[] = $row;
            }

            $hasMoreRaw = $payload['meta']['has_more'] ?? false;
            $hasMore = filter_var($hasMoreRaw, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            $hasMore = $hasMore ?? false;

            if (!$hasMore) {
                break;
            }

            $page++;
        }

        return $allRows;
    }
}
