<?php 
 
namespace App\Model;
 
use App\Model\BaseModel;
 
class OrderInfo extends BaseModel
{ 
 	protected $table = 'order_info';
 
 	protected $fillable = [ 
		 'id',  
		 'order_sn', // 订单编号 
		 'user_id', // 用户id 
		 'product_id', // 商品id 
		 'status', // 状态 
		 'closed_at', // 关闭时间 
		 'created_at', // 创建时间 
		 'updated_at', // 更新时间 
		 'deleted_at', // 删除时间
 	 ];
 }