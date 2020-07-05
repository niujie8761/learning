<?php
namespace Modules\Blog\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * controller 基类
 *
 * Class BaseController
 * @package Modules\Blog\Controllers
 */
class BaseController
{
    /**
     * Brief:获取用户信息
     *
     * @return mixed
     */
    protected function getUser()
    {
        return Auth::guard(Request()->guard)->user();
    }

    /**
     * Brief:获取用户id
     *
     * @return mixed
     */
    protected function getUserId()
    {
        return $this->getUser()->id;
    }

    /**
     * Brief:统一返回
     *
     * @param $token
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }
}
