<?php
namespace App\Model\Admin;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class AdminUsers extends Authenticatable implements JWTSubject
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

 	public function getJWTCustomClaims()
    {
        // TODO: Implement getJWTCustomClaims() method.
        return [];
    }

    public function getJWTIdentifier()
    {
        // TODO: Implement getJWTIdentifier() method.
        return $this->getKey();
    }
}
