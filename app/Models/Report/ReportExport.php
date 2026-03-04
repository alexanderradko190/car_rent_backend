<?php

namespace App\Models\Report;

use Illuminate\Database\Eloquent\Model;

class ReportExport extends Model
{
    protected $table = 'report_exports';

    protected $fillable = [
        'type',
        'from',
        'to',
        'status',
        'file_path',
        'error',
    ];

    protected $casts = [
        'from' => 'date',
        'to' => 'date',
    ];
}
