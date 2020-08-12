<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class Async implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $roomId;

    /**
     * Create a new job instance.
     *
     * @param $roomId
     * @return void
     */
    public function __construct($roomId)
    {
        //
        $this->roomId = $roomId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        recordLog($this->roomId, 'logs/job/handle.log');
    }

    /**
     * Brief:
     * @param \Exception $exception
     */
    public function failed(\Exception $exception)
    {
        recordLog($exception->getMessage(), 'logs/job/failed.log');
    }
}
