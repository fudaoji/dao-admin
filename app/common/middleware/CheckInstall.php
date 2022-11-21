<?php

namespace app\common\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

/**
 * Class Check install
 * @package app\middleware
 */
class CheckInstall implements MiddlewareInterface
{
    public function process(Request $request, callable $next): Response
    {

        $app = trim(\request()->getApp(), '/');
        if($app === 'install'){
            set_time_limit(0);
            require_once app_path() . '/install/common.php';

            if (strtolower(request()->getAction()) != 'complete' && is_file(base_path() . '/install.lock')) {
                return redirect(url('admin/index/index'));
            }
        }else{
            if (! file_exists(base_path() . '/.env')) {
                return redirect(url('install/index/index'));
            }
        }

        /** @var Response $response */
        $response = $next($request);
        return $response;
    }
}
