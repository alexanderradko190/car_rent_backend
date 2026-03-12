<?php

namespace App\Http\Controllers\Report;

use App\Enums\Report\ReportStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Report\ReportGenerateRequest;
use App\Jobs\GenerateReportJob;
use App\Repositories\Report\ReportRepositoryInterface;
use App\Services\Report\ReportStorage;
use Illuminate\Http\JsonResponse;

class ReportExportController extends Controller
{
    public function __construct(
        private readonly ReportRepositoryInterface $reportRepository,
    ) {
        //
    }

    public function create(ReportGenerateRequest $request): JsonResponse
    {
        $data = $request->validated();

        $report = $this->reportRepository->create($data);

        $job = GenerateReportJob::dispatch($report->id, $data)->onQueue('go-report');

        if (!$job) {
            return response()->json([
                'message' => 'Не удалось сгенерировать отчет'
            ], 500);
        }

        return response()->json([
            'id' => $report->id,
            'status' => $report->status,
        ], 202);
    }

    public function getReportStatus(int $id): JsonResponse
    {
        $report = $this->reportRepository->getReportById($id);

        if (!$report) {
            return response()->json([
                'message' => 'Отчет не найден',
            ], 404);
        }

        return response()->json([
            'status' => $report->status
        ]);
    }

    public function getFinishedReports(): JsonResponse
    {
        $reports = $this->reportRepository->getFinishedReports();

        return response()->json([
            'data' => $reports
        ]);
    }

    public function download(int $id, ReportStorage $reportStorage)
    {
        $report = $this->reportRepository->getReportById($id);

        if (!$report) {
            return response()->json([
                'message' => 'Отчет не найден',
            ], 404);
        }

        if ($report->status !== ReportStatus::FINISHED->value) {
            return response()->json([
                'message' => 'Отчет еще не готов'
            ], 400);
        }

        $path = ltrim($report->file_path, '/');

        if (!$reportStorage->exists($path)) {
            return response()->json([
                'message' => 'Файл не найден',
            ], 404);
        }

        return $reportStorage->download($path);
    }
}
