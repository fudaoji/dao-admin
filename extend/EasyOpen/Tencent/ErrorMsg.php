<?php
/**
 * Created by PhpStorm.
 * Script Name: ErrorMsg.php
 * Create: 2023/2/4 17:57
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace EasyOpen\Tencent;

class ErrorMsg
{
    /**
     * 翻译响应状态码
     * @param int $code
     * @param string $default
     * @return string
     * @author fudaoji<fdj@kuryun.cn>
     */
    public static function getErrorMsg($code = -1, $default = '未知错误')
    {
        $msg_list = [
            40001 => '不合法的调用凭证',
            40002 => '不合法的grant_type',
            40003 => '不合法的OpenID',
            40004 => '不合法的媒体文件类型',
            40007 => '不合法的media_id',
            40008 => '不合法的message_type',
            40009 => '不合法的图片大小',
            40010 => '不合法的语音大小',
            40011 => '不合法的视频大小',
            40012 => '不合法的缩略图大小',
            40013 => '不合法的AppID',
            40014 => '不合法的access_token',
            40015 => '不合法的菜单类型',
            40016 => '不合法的菜单按钮个数',
            40017 => '不合法的按钮类型',
            40018 => '不合法的按钮名称长度',
            40019 => '不合法的按钮KEY长度',
            40020 => '不合法的url长度',
            40023 => '不合法的子菜单按钮个数',
            40024 => '不合法的子菜单类型',
            40025 => '不合法的子菜单按钮名称长度',
            40026 => '不合法的子菜单按钮KEY长度',
            40027 => '不合法的子菜单按钮url长度',
            40029 => '不合法或已过期的code',
            40030 => '不合法的refresh_token',
            40036 => '不合法的template_id长度',
            40037 => '不合法的template_id',
            40039 => '不合法的url长度',
            40048 => '不合法的url域名',
            40054 => '不合法的子菜单按钮url域名',
            40055 => '不合法的菜单按钮url域名',
            40066 => '不合法的url',
            41001 => '缺失access_token参数',
            41002 => '缺失appid参数',
            41003 => '缺失refresh_token参数',
            41004 => '缺失secret参数',
            41005 => '缺失二进制媒体文件',
            41006 => '缺失media_id参数',
            41007 => '缺失子菜单数据',
            41008 => '缺失code参数',
            41009 => '缺失openid参数',
            41010 => '缺失url参数',
            41030 => 'page页面不存在，或者小程序没有发布',
            41033 =>'只允许通过api创建的小程序使用',
            42001 => 'access_token超时',
            42002 => 'refresh_token超时',
            42003 => 'code超时',
            43001 => '需要使用GET方法请求',
            43002 => '需要使用POST方法请求',
            43003 => '需要使用HTTPS',
            43004 => '需要订阅关系',
            43101 => '用户拒绝接受消息',
            44001 => '空白的二进制数据',
            44002 => '空白的POST数据',
            44003 => '空白的news数据',
            44004 => '空白的内容',
            44005 => '空白的列表',
            45001 => '二进制文件超过限制',
            45002 => 'content参数超过限制',
            45003 => '参数超过限制',
            45004 => 'description参数超过限制',
            45005 => 'url参数长度超过限制',
            45006 => 'picurl参数超过限制',
            45007 => '播放时间超过限制（语音为60s最大）',
            45008 => 'article参数超过限制',
            45009 => '接口调动频率超过限制',
            45010 => '建立菜单被限制',
            45011 => '频率限制',
            45012 => '模板大小超过限制',
            45016 => '不能修改默认组',
            45017 => '修改组名过长',
            45018 => '组数量过多',
            47003 => '模板参数不准确',
            48001 => '未取得接口授权',
            50001 => '接口未授权',
            61007 => '该小程序或者公众号绑定了多个平台，去小程序或者公众号的后台取消授权，然后重新绑定即可',

            85015 => '该账号不是小程序账号',
            85016 => '域名数量超过限制',
            85017 => '没有新增域名，请确认小程序已经添加了域名或该域名是否没有在第三方平台添加',
            85018 => '域名没有在第三方平台设置',
            89019 => '业务域名无更改，无需重复设置',
            89020 => '尚未设置小程序业务域名，请先在第三方平台中设置小程序业务域名后在调用本接口',
            89021 => '请求保存的域名不是第三方平台中已设置的小程序业务域名或子域名',
            89029 => '业务域名数量超过限制',
            89231 => '个人小程序不支持调用setwebviewdomain 接口',

            91001 => '不是公众号快速创建的小程序',
            91002 => '小程序发布后不可改名',
            91003 => '改名状态不合法',
            91004 => '昵称不合法',
            91005 => '昵称命中主体保护',
            91006 => '昵称命中微信号',
            91007 => '昵称已被占用',
            91008 => '昵称命中7天侵权保护期',
            91009 => '需要提交材料',
            91010 => '其他错误',

            91011 => '查不到昵称修改审核单信息',
            91012 => '其它错误',

            53010 => '名称格式不合法',
            53011 => '名称检测命中频率限制',
            53012 => '禁止使用该名称',
            53013 => '公众号：名称与已有公众号名称重复;小程序：该名称与已有小程序名称重复',
            53014 => '公众号：公众号已有{名称A+}时，需与该帐号相同主体才可申请{名称A};小程序：小程序已有{名称A+}时，需与该帐号相同主体才可申请{名称A}',
            53015 => '公众号：该名称与已有小程序名称重复，需与该小程序帐号相同主体才可申请;小程序：该名称与已有公众号名称重复，需与该公众号帐号相同主体才可申请',
            53016 => '公众号：该名称与已有多个小程序名称重复，暂不支持申请;小程序：该名称与已有多个公众号名称重复，暂不支持申请',
            53017 => '公众号：小程序已有{名称A+}时，需与该帐号相同主体才可申请{名称A};小程序：公众号已有{名称A+}时，需与该帐号相同主体才可申请{名称A}',
            53018 => '名称命中微信号',
            53019 => '名称在保护期内',

            40097 => '参数错误',
            46001 => 'media_id不存在',
            47001 => '参数格式非法',
            53202 => '本月头像修改次数已用完',

            53200 => '本月功能介绍修改次数已用完',
            53201 => '功能介绍内容命中黑名单关键字',

            85060 => '无效的taskid',
            85027 => '身份证绑定管理员名额达到上限',
            85061 => '手机号绑定管理员名额达到上限',
            85026 => '微信号绑定管理员名额达到上限',
            85063 => '身份证黑名单',
            85062 => '手机号黑名单',

            53300 => '超出每月次数限制',
            53301 => '超出可配置类目总数限制',
            53302 => '当前账号主体类型不允许设置此种类目',
            53303 => '提交的参数不合法',
            53304 => '与已有类目重复',
            53305 => '包含未通过IPC校验的类目',
            53306 => '修改类目只允许修改类目资质，不允许修改类目ID',
            53307 => '只有审核失败的类目允许修改',
            53308 => '审核中的类目不允许删除',

            85001 => '微信号不存在或微信号设置为不可搜索',
            85002 => '小程序绑定的体验者数量达到上限',
            85003 => '微信号绑定的小程序体验者达到上限',
            85004 => '微信号已经绑定',
            85013 => '无效的自定义配置',
            85014 => '无效的模版编号',
            85043 => '模版错误',
            85044 => '代码包超过大小限制',
            85045 => 'ext_json有不存在的路径',
            85046 => 'tabBar中缺少path',
            85047 => 'pages字段为空',
            85048 => 'ext_json解析失败',
            860003 => '不是由第三方代小程序进行调用',
            860013 => '不存在第三方的已经提交的代码',
            86000 => '不是由第三方代小程序进行调用',
            86001 => '不存在第三方的已经提交的代码',
            85006 => '标签格式错误',
            85007 => '页面路径错误',
            85008 => '类目填写错误',
            85009 => '已经有正在审核的版本',
            85010 => 'item_list有项目为空',
            85011 => '标题填写错误',
            85023 => '审核列表填写的项目数不在1-5以内',
            85077 => '小程序类目信息失效（类目中含有官方下架的类目，请重新选择类目）',
            86002 => '小程序还未设置昵称、头像、简介。请先设置完后再重新提交。',
            85085 => '近7天提交审核的小程序数量过多，请耐心等待审核完毕后再次提交',
            85086 => '提交代码审核之前需提前上传代码',
            85012 => '无效的审核id',
            85019 => '没有审核版本',
            85020 => '审核状态未满足发布',
            85021 => '状态不可变',
            85022 => 'action非法',
            87011 => '现网已经在灰度发布，不能进行版本回退',
            87012 => '该版本不能回退，可能的原因：1:无上一个线上版用于回退 2:此版本为已回退版本，不能回退 3:此版本为回退功能上线之前的版本，不能回退',
            85066 => '链接错误',
            85068 => '测试链接不是子链接',
            85069 => '校验文件失败',
            85070 => '链接为黑名单',
            85071 => '已添加该链接，请勿重复添加',
            85072 => '该链接已被占用',
            85073 => '二维码规则已满',
            85074 => '小程序未发布, 小程序必须先发布代码才可以发布二维码跳转规则',
            85075 => '个人类型小程序无法设置二维码规则',
            85076 => '链接没有ICP备案',
            87013 => '撤回次数达到上限（每天一次，每个月10次）',
            85079 => '小程序没有线上版本，不能进行灰度',
            85080 => '小程序提交的审核未审核通过',
            85081 => '无效的发布比例',
            85082 => '当前的发布比例需要比之前设置的高',

            9400001 => '该开发小程序已开通小程序直播权限，不支持发布版本。如需发版，请解绑开发小程序后再操作。 hint: [nGNdlo4FE-fOdtwA]',
            61500 => '日期格式不合法',
            61503 => '数据没有准备好，请稍后重试',
        ];

        return isset($msg_list[$code]) ? $msg_list[$code] : $default;
    }
}