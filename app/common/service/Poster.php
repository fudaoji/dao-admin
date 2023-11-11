<?php
/**
 * Created by PhpStorm.
 * Script Name: Poster.php
 * Create: 2023/10/29 17:21
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace app\common\service;
use Intervention\Image\ImageManagerStatic as Image;

class Poster
{

    /**
     * 生成海报
     * @param array $list
     * @return array|bool|string
     * Author: fudaoji<fdj@kuryun.cn>
     */
    static function generate($list = [])
    {
        Image::configure(['driver' => 'imagick']);
        //背景图片
        $image = Image::make($list['bg']);
        foreach ($list['items'] as $params){
            switch ($params['type']){
                case 'text':
                    if(empty($params['value'])){
                        break;
                    }
                    try {
                        $image->text($params['value'], $params['position'][0], $params['position'][1], function ($font) use($params) {
                            $font->file(empty($params['family']) ? '/usr/share/fonts/chinese/MSYH.TTF' : $params['family']);
                            $font->size($params['size']);
                            $font->color($params['color']);
                            !empty($params['align']) && $font->align($params['align']);
                            !empty($params['valign']) && $font->valign($params['valign']);
                        });
                    }catch(\Exception $e){
                        dao_log()->error('写' . $params['title'].'错误：' . json_encode($e->getMessage(), JSON_UNESCAPED_UNICODE));
                        //return false;
                    }
                    break;
                case 'image':
                    if(empty($params['value'])){
                        break;
                    }
                    $flag = false;
                    $count = 0;
                    while ($flag === false && $count < 10) {
                        $count++;
                        try {
                            $head = Image::make($params['value']);
                            if(!empty($params['size'])){
                                $head = $head->fit($params['size'][0], $params['size'][1]);
                            }
                            if(!empty($params['corner'])){
                                //$head->filter(new RoundCornerFilter($params['corner'][0]));
                                //$head->getCore()->roundCorners($params['corner'][0], $params['corner'][1]); //getCore()方法指向了原生的Imagick类的对象
                            }
                            $image->insert($head, $params['position_name'], $params['position'][0], $params['position'][1]);
                            $flag = true;
                        } catch (\Exception $e) {
                            dao_log()->error($params['title'] . '放入背景图出错：' . json_encode($e->getMessage()));
                            //return false;
                        }
                    }
                    break;
                case 'line':
                    try {
                        $image->line($params['point1'][0], $params['point1'][1], $params['point2'][0],$params['point2'][1], function ($draw) use($params) {
                            $draw->color($params['color']);
                            !empty($params['with']) && $draw->with($params['with']);
                        });
                    }catch(\Exception $e){
                        dao_log()->error('画' . $params['title'].'错误：' . json_encode($e->getMessage(), JSON_UNESCAPED_UNICODE));
                        //return false;
                    }
                    break;
            }
        }

        try {
            $pic_name = ($list['pic_name'] ?? get_rand_char(20)) . '.png';
            $stream = $image->stream('png', 80)->getContents();
            return Upload::instance(dao_config('system.upload'))
                ->putString($stream, $pic_name);
        } catch (\Exception $e) {
            return '保存海报失败： ' . json_encode($e->getMessage(), JSON_UNESCAPED_UNICODE);
        }
    }
}