<?php
namespace Modules\Blog\Controllers\Admin;

use App\Model\OrderInfo;
use App\Service\OrderService;
use Modules\Blog\Controllers\BaseController;

class MqController extends BaseController
{
    public function downOrder()
    {
        //创建订单
        $order = new OrderInfo();
        $order->order_sn = date('YmdHis').time();
        $order->user_id = 1;
        $order->product_id = 1;
        $order->save();

        (new OrderService())->push($order);

        return true;
    }
}
