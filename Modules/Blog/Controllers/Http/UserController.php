<?php
namespace Modules\Blog\Controllers\Http;
use Illuminate\Http\Request;
use Modules\Blog\Controllers\BaseController;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * 用户相关
 * Class UserController
 * @package Modules\Blog\Controllers\Http
 */
class UserController extends BaseController
{
    /**
     * Brief:获取用户信息
     *
     * @return mixed
     */
    public function info()
    {
        return $this->getUser();
    }

    /**
     * Brief:用户登录
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->only(['email', 'password']);
        if (! $token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }
}
