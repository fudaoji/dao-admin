<?php

namespace app\common\exception;

use Psr\SimpleCache\InvalidArgumentException;
use support\exception\BusinessException;
use Webman\Http\Request;
use Webman\Http\Response;
use Throwable;

class ExceptionHandle extends \Webman\Exception\ExceptionHandler
{
    public $dontReport = [
        BusinessException::class,
    ];

    public function report(Throwable $exception)
    {
        try {
            if (!empty($exception->getMessage())) {

                $data = [
                    'module'     => request()->app,
                    'controller' => request()->controller,
                    'action'     => request()->action,
                    'params'     => serialize(request()->all()),
                    'method'     => request()->method(),
                    'url'        => request()->url(),
                    'ip'         => request()->getRealIp()
                ];

                if (empty($data['name'])) {
                    $data['name'] = 'system';
                }

                $data['type'] = 1;
                $data['code'] = $exception->getCode();
                $data['file'] = $exception->getFile();
                $data['line'] = $exception->getLine();
                $data['error'] = $exception->getMessage();
            }

        } catch (InvalidArgumentException $e) {
        }
        parent::report($exception);
    }

    public function render(Request $request, Throwable $exception): Response
    {
        if (!file_exists(base_path(). '/.env')) {
            return parent::render($request, $exception);
        }
        $code = $exception->getCode();
        if ($request->expectsJson()) {
            $json = ['code' => $code ? $code : 500, 'msg' => $this->_debug ? $exception->getMessage() : 'Server internal error'];
            $this->_debug && $json['traces'] = \explode("\n", (string)$exception);
            return new Response(200, ['Content-Type' => 'application/json'],
                \json_encode($json, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }

        return getenv('APP_DEBUG') ? parent::render($request, $exception) : view(config('app.exception_tpl'), ['trace' => \nl2br($exception->getTraceAsString())]);
    }
}