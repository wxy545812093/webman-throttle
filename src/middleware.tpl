<?php
/**
 * @name 节流设置
 * @author vipkwd <service@vipkwd.com>
 * @link https://github.com/wxy545812093/webman-throttle
 * @license MIT License
 * @copyright The PHP-Tools
 */
declare(strict_types = 1);

namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;
use Vipkwd\WebmanMiddleware\Throttle as ThrottleCore;

/**
 * Class StaticFile
 * @package app\middleware
 */
class Throttle implements MiddlewareInterface
{
    public function process(Request $request, callable $next, array $params = []):Response
    {
        return (new ThrottleCore)->handle($request, $next, $params);
    }
}