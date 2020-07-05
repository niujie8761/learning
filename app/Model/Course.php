<?php 
 
namespace App\Model;
 
use App\Model\BaseModel;
 
class Course extends BaseModel
{ 
 	protected $table = 'course';
 
 	protected $fillable = [ 
		 'id',  
		 'name', // 名称 
		 'created_at', // 创建时间 
		 'updated_at', // 更新时间 
		 'deleted_at', // 删除时间
 	 ];
 }