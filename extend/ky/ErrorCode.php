<?php
namespace ky;

class ErrorCode
{
    const TokenInvalid  = 0;    // 捕获异常
    const SuccessCode     = 1;    // 请求成功, 结果不为空
    const EmptyResult     = 2;    // 请求成功, 结果为空
    const FailedCode      = 2000; // 非法请求，请求失败

    const NotLogin    = 911;  // session过期, 重定向ajax请求

    const ErrorParam      = 2001; // 参数格式错误
    const InvalidParam    = 2002; // 参数值非法
    const BadParam        = 2003; // 参数值非法, 服务端强制客户端提示
    const RepeatSubmit    = 2004; // 表单重复提交
    const IlleglOperation = 2005; // 非法操作
    const AuthExpired     = 2006; // 权限过期

    const RException      = 3000; // redis连接异常

    const UploadExcepion  = 3500; // 文件上传异常

    const InvalidShardKey = 4000; // shardid非法

    const QiniuException  = 4500; // 七牛异常

    const CurlError       = 9000; // curl发生错误
    const HttpError       = 9001; // http错误
    const EncodeError     = 9002; // SDK编码错误

    const ParamException   = 50000; // 参数异常
    const ClassNotExist    = 51000; // 类不存在

    const DbException      = 52000; // 数据库异常
    const CommandException = 53000; // 命令行库异常
    const WeixinException  = 54000; // 微信库异常
    const WxpayException   = 54100; // 微信支付异常
    const WxCompException  = 54200; // 微信开放平台组件异常
    const AlipayException  = 55000; // 支付宝异常
    const QyWeixinException  = 56000; // 企业微信库异常
    const InitException    = 100000; // 初始化异常

    const SMSError   = 200000; // 发送短信接口异常
}