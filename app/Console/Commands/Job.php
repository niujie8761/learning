<?php

namespace App\Console\Commands;

use App\Jobs\Async;
use Illuminate\Bus\Dispatcher;
use Illuminate\Console\Command;

class Job extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'job';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $job = (new Async(100))->onQueue('async');

        $res = app(Dispatcher::class)->dispatch($job);

        recordLog('111', 'logs/job/command.log');
    }
}
