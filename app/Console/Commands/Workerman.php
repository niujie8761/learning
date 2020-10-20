<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Workerman\Worker;

class Workerman extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Workerman {action} {--daemonize}';

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
        global $argv;//定义全局变量
        $arg = $this->argument('action');
        $argv[1] = $arg;
        $argv[2] = $this->option('daemonize') ? '-d' : '';//该参数是以daemon（守护进程）方式启动
        global $text_worker;
        // 创建一个Worker监听2345端口，使用websocket协议通讯
        $text_worker = new Worker("websocket://0.0.0.0:2345");
        $text_worker->uidConnections = array();//在线用户连接对象
        $text_worker->uidInfo = array();//在线用户的用户信息
        // 启动4个进程对外提供服务
        $text_worker->count = 4;
        //引用类文件
        $handler = app()->make('App\Handler\WorkermanHandler');
        $text_worker->onConnect = array($handler,"handle_connection");
        $text_worker->onMessage = array($handler,"handle_message");
        $text_worker->onClose = array($handler,"handle_close");
        $text_worker->onWorkerStart = array($handler,"handle_start");
        // 运行worker
        Worker::runAll();
    }
}
