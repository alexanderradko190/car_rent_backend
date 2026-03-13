<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class TransactionHelper
{
    public function run(callable $callback): mixed
    {
        return DB::transaction($callback);
    }
}
