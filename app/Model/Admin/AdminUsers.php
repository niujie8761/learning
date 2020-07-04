<?php 
 
namespace App\Models\Admin;
 
use App\Models\BaseModel;
 
class AdminUsers extends BaseModel
{ 
 	protected $table = 'admin_users';
 
 	protected $fillable = [ 
		 'id',  
		 'mobile', // 手机号码 
		 'password', // 密码 
		 'created_at', // 创建时间 
		 'updated_at', // 更新时间 
		 'deleted_at', // 删除时间
 	 ];
 }