<?php

namespace App\Http\Controllers\Export;

use App\Http\Controllers\Controller;
use App\Services\Export\GoExportService;

class ExportController extends Controller
{
    public function export(GoExportService $service, $type)
    {
        $data = $service->export($type);
        return response()->json($data);
    }
}
