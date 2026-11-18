<?php

namespace App\Traits;

trait DocumentNumber
{
    public static function generateSequenceNumber(string $prefix = 'DOC'): string
    {
        $date = date('Ymd');
        $random = strtoupper(substr(uniqid(), -4));
        return "{$prefix}-{$date}-{$random}";
    }
}
