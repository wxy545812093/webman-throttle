<?php
/**
 * @name 节流设置
 * @author vipkwd <service@vipkwd.com>
 * @link https://github.com/wxy545812093/webman-throttle
 * @license MIT License
 * @copyright The PHP-Tools
 */

use Vipkwd\WebmanMiddleware\Throttle;
use Vipkwd\WebmanMiddleware\ThrottleCore\CounterFixed;
// use Vipkwd\WebmanMiddleware\ThrottleCore\CounterSlider;
// use Vipkwd\WebmanMiddleware\ThrottleCore\TokenBucket;
// use Vipkwd\WebmanMiddleware\ThrottleCore\LeakyBucket;
use support\{Request, Response};


/**
 * $routes_rate 应用场景:
 * 
 *   -- 截至(Webman 1.2.5, Webman-framework v1.2.7) 不支持路由中间件默认传参。
 * 
 *   故在此主动申明各路由绑定的 限流规则
 * 
 */
$routes_rate = [
    [
        //maps 内配置的route PATH复用本组配置
        'maps' => ['/v1/api/user/info', '/v1/api/user/assets'],

        // 要被限制的请求类型, eg: GET POST PUT DELETE HEAD
        'visit_method' => ['GET','POST'],
        
        // 设置访问频率，例如 '10/m' 指的是允许每分钟请求10次。值 null 表示不限制,
        // eg: null 10/m  20/h  300/d 200/300
        'visit_rate' => '2/10',  
    ],
];

return [

    'routes_rate' => $routes_rate ?? [],

    // 缓存键前缀，防止键值与其他应用冲突
    'prefix' => 'throttle_',
	
    // 缓存的键，true 表示使用来源ip (request->getRealIp(true))
    'key' => true,
	
    // 响应体中设置速率限制的头部信息，含义见：https://docs.github.com/en/rest/overview/resources-in-the-rest-api#rate-limiting
    'visit_enable_show_rate_limit' => true,
    
    // ----------·----------·----------·----------·----------
    // 要被限制的请求类型, eg: GET POST PUT DELETE HEAD
    'visit_method' => ['GET'],
	
    // 设置访问频率，例如 '10/m' 指的是允许每分钟请求10次。值 null 表示不限制,
	// eg: null 10/m  20/h  300/d 200/300
    'visit_rate' => '2/10',
    
    // 访问受限时返回的响应( type: null|callable )
    'visit_fail_response' => function (Throttle $throttle, Request $request, int $wait_seconds): Response {
        $msg = 'Too many requests, try again after ' . $wait_seconds . ' seconds.';
        return !$request->isAjax() ? response($msg) : json([
            'code' => 429,
            'msg' => $msg,
            'data' => null
        ]);
    },

    // ----------·----------·----------·----------·----------
	
    /*
     * 设置节流算法，组件提供了四种算法：
     *  - CounterFixed ：计数固定窗口
     *  - CounterSlider: 滑动窗口
     *  - TokenBucket : 令牌桶算法
     *  - LeakyBucket : 漏桶限流算法
     */
    'driver_name' => CounterFixed::class,
	
    // Psr-16通用缓存库规范: https://blog.csdn.net/maquealone/article/details/79651111
    // Cache驱动必须符合PSR-16缓存库规范，最低实现get/set俩个方法 (且需静态化实现)
    //    static get(string $key, mixed $default=null)
    //    static set(string $key, mixed $value, int $ttl=0);

    //webman默认使用 symfony/cache作为cache组件(https://www.workerman.net/doc/webman/db/cache.html)
	'cache_drive' => support\Cache::class,
    
    //使用ThinkCache
    // 'cache_drive' => think\facade\Cache::class,	
];