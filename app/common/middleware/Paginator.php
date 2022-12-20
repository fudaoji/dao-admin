<?php
namespace app\common\middleware;

use ky\Laypage;
use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Paginator implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        \think\Paginator::maker(function ($items, $listRows, $currentPage, $total, $simple, $options) {
            return new Laypage($items, $listRows, $currentPage, $total, $simple, $options);
        });
        return $handler($request);
    }
}