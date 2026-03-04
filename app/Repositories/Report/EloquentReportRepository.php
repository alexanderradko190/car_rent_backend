<?php

namespace App\Repositories\Report;

use App\Enums\Report\ReportStatus;
use App\Models\Report\ReportExport;
use Illuminate\Support\Collection;

class EloquentReportRepository implements ReportRepositoryInterface
{
    public function create(array $data): ReportExport
    {
        return ReportExport::create([
            'type' => $data['type'],
            'from' => $data['date_from'],
            'to' => $data['date_to'],
            'status' => ReportStatus::PENDING->value
        ]);
    }

    public function updateReport(
        $reportId,
        ReportStatus $reportStatus,
        $filePath = null,
        $error = null,
    ): void {
        $report = $this->getReportById($reportId);

        $report->update([
            'status' => $reportStatus->value,
            'error' => $error,
            'file_path' => $filePath
        ]);
    }

    public function getReportById(int $reportId): ReportExport
    {
        return ReportExport::query()->findOrFail($reportId);
    }

    public function getFinishedReports(): Collection
    {
        return ReportExport::query()
            ->where('status', ReportStatus::FINISHED->value)
            ->orderByDesc('id')
            ->get();
    }
}
