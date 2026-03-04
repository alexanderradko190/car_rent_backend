<?php

namespace App\Repositories\Report;

use App\Enums\Report\ReportStatus;
use App\Models\Report\ReportExport;
use Illuminate\Support\Collection;

interface ReportRepositoryInterface
{
    public function create(array $data): ReportExport;

    public function updateReport(
        $reportId,
        ReportStatus $reportStatus,
        $filePath = null,
        $error = null,
    ): void;

    public function getReportById(int $reportId): ReportExport;

    public function getFinishedReports(): Collection;
}
