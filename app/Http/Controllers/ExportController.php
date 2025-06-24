<?php

namespace App\Http\Controllers;

use App\Services\GoExportService;

class ExportController extends Controller
{
    public function export(GoExportService $service, $type)
    {
        $data = $service->export($type);
        return response()->json($data);
    }
}
