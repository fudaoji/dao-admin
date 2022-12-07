<?php
/**
 * Created by PhpStorm.
 * Script Name: Base.php
 * Create: 2022/7/20 17:03
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace app;

use app\common\model\TenantInfo;
use ky\ErrorCode;
use support\Response;

class ApiController extends BaseController
{
    protected $captchaKey = 'captchaApi';

    public function __construct()
    {
        parent::__construct();
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
    protected function jSuccess($msg = '操作失败!', $data = '', int $code = 1, array $header = []): Response
    {
        $msg = dao_trans($msg);
        $result = [
            'code'  => $code,
            'msg'   => $msg,
            'data'  => $data
        ];
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
    protected function jError($msg = '操作失败!', int $code = ErrorCode::BadParam, $data = '', array $header = []): Response
    {
        $msg = dao_trans($msg);
        $result = [
            'code' => $code,
            'msg'  => $msg,
            'data' => $data
        ];
        return json($result);
    }

    protected function getAjax()
    {
        $json = \request()->rawBody();
        return $json ? json_decode($json, true) : [];
    }
}