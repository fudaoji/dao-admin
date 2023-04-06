<?php
/**
 * Created by PhpStorm.
 * Script Name: Notice.php
 * Create: 2023/4/6 10:46
 * Description: 系统公告
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace app\common\service;
use app\common\model\Notice as NoticeM;
use app\common\model\NoticeRead as ReadM;

class Notice
{
    /**
     * 获取客户已读公告数据
     * @param null $tenant_info
     * @param bool $refresh
     * @return array|mixed|\think\db\Query|\think\Model|null
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * Author: fudaoji<fdj@kuryun.cn>
     */
    static function getTenantRead($tenant_info = null, $refresh = false){
        is_null($tenant_info) && $tenant_info = Tenant::sessionTenantInfo();
        $key = __FUNCTION__ . $tenant_info['id'];
        $data = cache($key);
        if(empty($data) || $refresh){
            if(! $data = ReadM::where('tenant_id', $tenant_info['id'])->find()){
                $data = ['tenant_id' => $tenant_info['id'], 'notice' => ''];
                $data['id'] = ReadM::insertGetId($data);
            }
        }
        cache($key, $data);
        return $data;
    }

    /**
     * 获取客户的展示的最新十条公告
     * @param null $tenant_info
     * @return mixed|\think\Paginator
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * Author: fudaoji<fdj@kuryun.cn>
     */
    static function listTenantNewsNotices($tenant_info = null){
        $list = self::listNewsNotices();
        $read = self::getTenantRead($tenant_info);
        $read_list = explode(',', trim($read['notice'], ','));
        foreach ($list as $k => $value){
            $value['read'] = in_array($value['id'], $read_list) ? true : false;
            $list[$k] = $value;
        }
        return $list;
    }

    /**
     * 获取最新十条系统消息
     * @param bool $refresh
     * @return mixed|\think\Paginator
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * Author: fudaoji<fdj@kuryun.cn>
     */
    static function listNewsNotices($refresh = false){
        $key = __FUNCTION__ .'_system';
        $list = cache($key);
        if(empty($list) || $refresh){
            $list = NoticeM::order('publish_time', 'desc')
                ->paginate(10);
        }
        cache($key, $list);
        return $list;
    }

    /**
     * 设置已读
     * @param $id
     * @param null $tenant_info
     * @return array|mixed|\think\db\Query|\think\Model|null
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public static function setRead($id, $tenant_info = null)
    {
        $read = self::getTenantRead($tenant_info);
        $read_list = explode(',', trim($read['notice'], ','));
        if(!in_array($id, $read_list)){
            array_push($read_list, $id);
            ReadM::update([
                'id' => $read['id'],
                'notice' => ','.implode(',', $read_list).','
            ]);
            self::getTenantRead($tenant_info, true);
        }
        return $read;
    }

}