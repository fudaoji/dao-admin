<?php
declare (strict_types = 1);

namespace app;

use Gregwar\Captcha\PhraseBuilder;
use support\Response;
use think\helper\Str;
use think\Validate;
use Webman\Http\Request;
use Gregwar\Captcha\CaptchaBuilder;

class BaseController
{

    /**
     * 应用实例
     * @var $app
     */
    protected $app;

    /**
     * 获取访问来源
     * @var null
     */
    public $referer = null;
    protected $theme = 'default';

    protected $captchaKey = 'captcha';

    public function __construct()
    {
        $this->referer = \request()->header('referer');
    }

    /**
     * 统一视图
     * @param array $assign
     * @param string $view
     * @param null $app
     * @return mixed
     * @Author  fudaoji<fdj@kuryun.cn>
     */
    public function show($assign = [], $view = '', $app = null){
        $controller = \request()->getController();
        $action = \request()->getAction();
        if ($view) {
            $layer = explode('/', $view);
            switch(count($layer)){
                case 3:
                    $controller = $layer[1];
                    $action = $layer[2];
                    $app = $layer[0];
                    break;
                case 2:
                    $controller = $layer[0];
                    $action = $layer[1];
                    break;
                default:
                    $action = $layer[0];
                    break;
            }
        }
        $template = $this->theme. DIRECTORY_SEPARATOR. $controller . DIRECTORY_SEPARATOR . $action;
        return view($template, $assign, $app);
    }

    /**
     * 验证数据
     * @access protected
     * @param array $data 数据
     * @param string|array $validate 验证器名或者验证规则数组
     * @param array $message 提示信息
     * @param bool $batch 是否批量验证
     * @return bool|true
     */
    protected function validate(array $data, $validate, array $message = [], bool $batch = false): bool
    {
        if (is_array($validate)) {
            $v = new Validate();
            $v->rule($validate);
        } else {
            if (strpos($validate, '.')) {
                // 支持场景
                [$validate, $scene] = explode('.', $validate);
            }
            $class = false !== strpos($validate, '\\') ? $validate : $this->parseClass('common\validate', $validate);
            $v     = new $class();
            if (!empty($scene)) {
                $v->scene($scene);
            }
        }

        $v->message($message);

        // 是否批量验证
        if ($batch) {
            $v->batch();
        }

        return $v->failException()->check($data);
    }

    /**
     * 解析应用类的类名
     * @access public
     * @param string $layer 层名 controller model ...
     * @param string $name  类名
     * @return string
     */
    protected function parseClass(string $layer, string $name): string
    {
        $name  = str_replace(['/', '.'], '\\', $name);
        $array = explode('\\', $name);
        $class = Str::studly(array_pop($array));
        $path  = $array ? implode('\\', $array) . '\\' : '';
        return 'app'. '\\' . $layer . '\\' . $path . $class;
    }

    /**
     * 操作成功跳转的快捷方法
     * @access protected
     * @param mixed $msg 提示信息
     * @param string|null $url 跳转的URL地址
     * @param mixed $data 返回的数据
     * @param int $code
     * @param integer $wait 跳转等待时间
     * @param array $header 发送的Header信息
     * @return Response
     */
    protected function success($msg = '', string $url = '', $data = '', int $code = 1, int $wait = 3, array $header = []): Response
    {
        if (is_null($url) && \request()->header('referer')) {
            $url = \request()->header('referer');
        }
        //$msg = !empty($msg) ? dao_trans($msg) : dao_trans('操作成功!');
        $result = [
            'code'  => $code,
            'msg'   => $msg,
            'data'  => $data,
            'url'   => (string)$url,
            'wait'  => $wait,
        ];
        $type = $this->getResponseType();
        if ($type == 'html') {
            return view(config('app.dispatch_success'), $result);
        }

        return json($result);
    }

    /**
     * 操作错误跳转的快捷方法
     * @access protected
     * @param mixed $msg 提示信息
     * @param null $url 跳转的URL地址
     * @param mixed $data 返回的数据
     * @param int $code
     * @param integer $wait 跳转等待时间
     * @param array $header 发送的Header信息
     * @return Response
     */
    protected function error($msg = '', $url = null, $data = '', int $code = 0, int $wait = 3, array $header = []): Response
    {
        if (is_null($url)) {
            $url = request()->isAjax() ? '' : 'javascript:history.back(-1);';
        }

        $msg = !empty($msg) ? dao_trans($msg) : dao_trans('操作失败!');
        $result = [
            'code' => $code,
            'msg'  => $msg,
            'data' => $data,
            'url'  => (string)$url,
            'wait' => $wait,
        ];

        $type = $this->getResponseType();
        if ($type == 'html') {
            return view(config('app.dispatch_error'), $result);
        }

        return json($result);
    }

    /**
     * 获取当前的response 输出类型
     * @access protected
     * @return string
     */
    protected function getResponseType(): string
    {
        return request()->isAjax() || request()->acceptJson() ? 'json' : 'html';
    }

    /**
     * 输出验证码图像
     * @param Request $request
     * @return Response
     */
    public function captcha(Request $request): Response
    {
        $phrase = new PhraseBuilder;
        $code = $phrase->build(4, '0123456789');
        $builder = new CaptchaBuilder($code, $phrase);
        $builder->build();
        $builder->setDistortion(false);
        $request->session()->set($this->captchaKey, strtolower($builder->getPhrase()));
        $img_content = $builder->get();
        return response($img_content, 200, ['Content-Type' => 'image/jpeg']);
    }

    /**
     * 检查验证码
     * @param string $text
     * @return bool
     */
    protected function captchaCheck(string $text): bool
    {
        $captcha = $text ?? \request()->post($this->captchaKey);
        if (strtolower($captcha) !== \request()->session()->get($this->captchaKey)) {
            return false;
        }
        return true;
    }

    /**
     * URL重定向
     * @access protected
     * @param string $url 跳转的URL表达式
     * @param integer $code http code
     * @param array $headers
     * @return Response
     */
    protected function redirect(string $url, int $code = 302, array $headers = []): Response
    {
        return redirect($url, $code, $headers);
    }
}