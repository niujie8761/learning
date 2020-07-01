<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2019/10/26
 * Time: 12:31
 */
namespace App\Http\Middleware;

use App\Exceptions\FailedException;
use Illuminate\Support\Facades\Redis;


/**
 * @breif 接口限流
 *
 * Class SlidingGrant
 * @package App\Http\Middleware
 */
class SlidingGrant
{
    public function handle($request, \Closure $next, $guard = null)
    {
        if($guard) {
            $request->guard = $guard;
        }
        $requestUrl = $request->getRequestUri();
        $url = substr($requestUrl, 0, strpos($requestUrl, '?'));
        $interfaceLimit = $this->slidingWindowsGrant($url, $request->user($guard)->id ?? 0);
        if(!$interfaceLimit) {
            throw new FailedException($url.'接口请求频率达到上限, 请稍后再试');
        }
        return $next($request);
    }

    /**
     * @brief:滑块限流
     *
     * @param $url
     * @param int $uid
     * @date: 2019/10/26
     * @time: 12:58
     * @return bool
     */
    private function slidingWindowsGrant($url, $uid = 0)
    {
        $limit = config('grant.limit');
        $interval = config('grant.interval');
        $key = sprintf("request:%s:%s", $url, $uid);
        $now = time();
        Redis::multi();
        Redis::zadd($key, $now, $now);
        Redis::zremrangebyscore($key, 0, $now - $interval);
        Redis::zcard($key);
        Redis::expire($key, $interval + 1000);
        $replics = Redis::exec();
        return $replics[2] <= $limit;
    }

}
