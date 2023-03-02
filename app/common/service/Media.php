<?php
/**
 * Created by PhpStorm.
 * Script Name: Media.php
 * Create: 12/29/22 4:27 PM
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace app\common\service;

use app\common\model\MediaFile;
use app\common\model\MediaImage;
use app\common\model\MediaLink;
use app\common\model\MediaText;
use app\common\model\MediaVideo;
use app\common\service\Tenant as TenantService;

class Media
{
    const TEXT = "text";
    const IMAGE = "image";
    const FILE = "file";
    const VIDEO = "video";
    const VOICE = 'voice';
    const MUSIC = 'music';
    const LINK = "link";
    const NEWS = 'news';
    const APP = 'app';

    public static function types($id = null){
        $list = [
            self::TEXT => '文本',
            self::IMAGE => '图片',
            self::FILE => '文件',
            self::VIDEO => '视频',
            self::LINK => '分享链接'
        ];
        return isset($list[$id]) ? $list[$id] : $list;
    }

    static function mediaTabs(){
        return [
            self::IMAGE => ['title' => '图片', 'href' => url('mediaimage/index')],
            self::TEXT => ['title' => '文本', 'href' => url('mediatext/index')],
            self::FILE => ['title' => '文件', 'href' => url('mediafile/index')],
            self::VIDEO => ['title' => '视频', 'href' => url('mediavideo/index')],
            self::LINK => ['title' => '分享链接', 'href' => url('medialink/index')]
        ];
    }

    static function getModel($type = 'text'){
        $class_list = [
            self::TEXT => new MediaText(),
            self::IMAGE => new MediaImage(),
            self::FILE => new MediaFile(),
            self::VIDEO => new MediaVideo(),
            self::LINK => new MediaLink(),
        ];
        return $class_list[$type];
    }

    /**
     * 获取单个素材
     * @param array $params
     * @return mixed
     * Author: fudaoji<fdj@kuryun.cn>
     */
    static function getMedia($params = []){
        if(empty($params['company_id'])){
            $params['company_id'] = TenantService::getCompanyId();
        }
        return self::getModel($params['type'])->where([
            ['company_id', '=', $params['company_id']]
        ])->find($params['id']);
    }
}