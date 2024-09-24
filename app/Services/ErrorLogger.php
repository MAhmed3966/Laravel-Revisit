<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

 class ErrorLogger {
    public static function logAndThrow($e, $message = null)
    {
        if($message !==null){
            Log::info($e->getMessage(), ['exception' => $e->getTraceAsString(), 'message' => $message]);
        } else {
            Log::info($e->getMessage(), ['exception' => $e->getTraceAsString()]);
        }
        throw $e;
    }
}


