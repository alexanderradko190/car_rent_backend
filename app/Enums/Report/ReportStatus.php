<?php

namespace App\Enums\Report;

enum ReportStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case FINISHED = 'finished';
    case FAILED = 'failed';
}
