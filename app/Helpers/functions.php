<?php
use Illuminate\Support\Facades\Log;

function recordLog($message, $dir = 'logs/sms/sms.log')
{
    if (config('app.debug')) {
        config(['logging.channels.daily.path' => storage_path($dir)]);
        Log::channel('daily')->info($message);
    }
}
