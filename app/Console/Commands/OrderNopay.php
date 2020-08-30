<?php

namespace App\Console\Commands;

use App\Service\OrderService;
use Illuminate\Console\Command;

class OrderNopay extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:nopay';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '订单超时未支付';

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
     * @param OrderService $orderService
     *
     * @return mixed
     */
    public function handle(OrderService $orderService)
    {
        //
        $orderService->consume();
    }
}
