<?php
namespace Modules\Blog\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Blog\Controllers\BaseController;

/**
 * admin 登录
 * Class LoginController
 * @package Modules\Blog\Controllers\Admin
 */
class LoginController extends BaseController
{
    /**
     * Brief:admin 登录
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->only(['mobile', 'password']);

        if(!$token = auth('admin')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        recordLog(print_r($credentials, true), 'logs/login/info.log');
        return $this->respondWithToken($token);
    }
}
