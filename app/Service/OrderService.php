<?php
namespace App\Service;


use App\Model\OrderInfo;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

class OrderService
{
    const HOST = '101.132.105.173:15672';
    const PORT = '15672';
    const LOGIN = 'root';
    const PASSWORD = 'root';
    const VHOST = '/';

    //交换机名称
    public $exchangeName = 'laravel_exchange_name';

    //普通队列名称和路由Key
    public $queueName = 'laravel_queue_name';
    public $routeKey = 'laravel_route_key';

    //延迟队列和路由key
    public $delayQueueName = 'laravel_delay_queue_name';
    public $delayRouteKey = 'laravel_delay_route_key';

    //延迟时间
    public $delaySecond = 10;

    public $channel;

    public function __construct()
    {
        $connection = new AMQPStreamConnection(self::HOST, self::PORT, self::LOGIN, self::PORT);

        $this->channel = $connection->channel();

        $this->init();
    }

    public function init()
    {
        //声明交换机
        $this->channel->exchange_declare($this->exchangeName, 'direct', false, true, false);

        $this->declareConsumeQueue();

        $this->declareDelayQueue();
    }

    public function declareConsumeQueue()
    {
        //声明消费队列
        $this->channel->queue_declare($this->queueName, false, true, false, false);
        //绑定交换机及队列
        $this->channel->queue_bind($this->queueName, $this->exchangeName, $this->routeKey);
    }

    public function declareDelayQueue()
    {
        //设置消息过期时间
        $tab = new AMQPTable([
           'x-dead-letter-exchange'     =>  $this->exchangeName,//消息过期后推送到这交换机
            'x-dead-letter-routing-key' =>  $this->routeKey,//消息过期后推送到这路由地址
            'x-message-ttl'             =>  intval($this->delaySecond) * 1000, //10秒
        ]);

        //声明延迟队列
        $this->channel->queue_declare($this->delayQueueName, false, true, false, false, false, $tab);
        //绑定交换机及延迟队列
        $this->channel->queue_bind($this->delayQueueName, $this->exchangeName, $this->delayRouteKey);
    }

    //入队列
    public function push(OrderInfo $order)
    {
        $message = json_encode([
            'id'  => $order->id
        ]);

        //创建消息
        $msg = new AMQPMessage($message, [
            'delivery_mode' => AMQPMessage::DELIVERY_MODE_NON_PERSISTENT
        ]);

        //推送到队列                   //消息  //交换机名称          // 路由 推送到延迟队列中
        $this->channel->basic_publish($msg, $this->exchangeName, $this->delayRouteKey);
    }

    //出队列
    public function consume()
    {
        $this->channel->basic_consume($this->queueName, '', false, false, false, false,
        [$this, 'process_message']);

        while(count($this->channel->callbacks)) {
            $this->channel->wait();
        }
    }

    //开始消费
    public function process_message($message)
    {
        $obj = json_decode($message->body);
        try {

            $order = OrderInfo::query()->where('id', $obj->id)->first();
            if (strtotime($order->created_at) + $this->delaySecond > time()){
                throw new \Exception('取消订单时间未到', 404);
            }

            //更改数据库状态
            $order->status = 10;
            $order->colsed_at = date('Y-m-d H:i:s');
            $res = $order->save();

            if (!$res){
                throw new \Exception('取消订单失败', 404);
            }

        } catch (\Exception $e) {
            //记录日志
        }
        //确认消息处理完成
        $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);
    }
}
