<?php
namespace Modules\Blog\Controllers\Admin;

use App\Jobs\Async;
use Illuminate\Bus\Dispatcher;
use Illuminate\Http\Request;
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

    /**
     * Brief:admin job
     *
     * @return \Illuminate\Foundation\Bus\PendingDispatch
     */
    public function job()
    {
        $job = (new Async(100))->onQueue('async');

        $res = app(Dispatcher::class)->dispatch($job);

        dd($res);
    }
}
