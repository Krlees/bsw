<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Route;
use DB;
use Cache;

class CheckToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param  string|null $guard
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token = $request->input('token');
        if (!$this->checkToken($token)) {
            return false;
        }

        return $next($request);
    }

    public function checkToken($token)
    {
        if (empty($token))
            return false;

        // 查询缓存中有没有存储token
        if (!cache()->has($token)) {
            return false;
        }

        $userToken = \DB::table('user_token')->where('token', $token)->first();
        if (!$userToken) {
            return false;
        }

        // 查询用户基本信息
        $userData = \DB::table('user')->where('id', $userToken->user_id)->where('is_del', 0)->first();
        if (!$userData) {
            return false;
        }

        $userData = obj2arr($userData);
        unset($userToken->password);

        cache()->forever($token,$userData);

        return true;

    }
}
