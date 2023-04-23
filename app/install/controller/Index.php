<?php
namespace app\install\controller;

use think\facade\Db;

class Index extends Base
{
	protected array $status = array(
        'index'    => 'info',
        'check'    => 'info',
        'config'   => 'info',
        'sql'      => 'info',
        'complete' => 'info',
    );

    /**
     * 安装协议声明
     * @return mixed
     * Author: fudaoji<fdj@kuryun.cn>
     */
	public function index() {
		$this->status['index'] = 'primary';
		return $this->show(['status' => $this->status]);
	}

    /**
     * 环境检测
     * @return mixed
     * Author: fudaoji<fdj@kuryun.cn>
     */
	public function check() {
		session(['error' => false]);
		//环境检测
		$env = check_env();
		//目录文件读写检测
		if (IS_WRITE) {
			$dirfile = check_dirfile();
			$this->assign['dirfile'] = $dirfile;
		}
		//函数检测
		$func = check_func();
		session(['step'=> 1]);
		$this->assign['env'] = $env;
		$this->assign['func'] = $func;
		$this->status['index'] = 'success';
		$this->status['check'] = 'primary';
		$this->assign['status'] = $this->status;
		return $this->show();
	}

    /**
     * 系统配置
     * @return mixed|void
     * @throws \think\Exception
     * Author: fudaoji<fdj@kuryun.cn>
     */
	public function config() {
		if (request()->isPost()) {
		    $post_data = input('post.');
		    $db = $post_data['db'];
		    $admin = $post_data['admin'];
		    $redis = $post_data['redis'];
		    $memcache = $post_data['memcache'];

			//检测管理员信息
			if (empty($admin['username']) || empty($admin['password'] || empty($admin['repassword']))) {
                return $this->error('请填写完整管理员信息');
			} else if ($admin['password'] != $admin['repassword']) {
                return $this->error('两次输入的密码不一致');
			}
            //缓存管理员信息
            session(['admin_info'=> $admin]);

			//检测数据库配置
			if (empty($db['type']) || empty($db['hostname']) || empty($db['database']) || empty($db['username']) || empty($db['password'])
                || empty($db['port'])
            ) {
                return $this->error('请填写完整的数据库配置');
			} else {
				//缓存数据库配置
				session(['db_config' => $db]);
			}
			if(empty($post_data['cache_type'])){
                return $this->error('请选择缓存类型');
            }
			session(['cache_type' => $post_data['cache_type']]);
            session(['cache_prefix' => $post_data['cache_prefix']]);
			if($post_data['cache_type'] == 'memcache'){
                //检测memcache
                if(empty($memcache['memcache_host']) || empty($memcache['memcache_port'])){
                    return $this->error('请填写完整的Memcached配置');
                }
            }elseif ($post_data['cache_type'] == 'redis'){
                //检测redis
                if(empty($redis['redis_host']) || empty($redis['redis_port'])){
                    return $this->error('请填写完整的redis配置');
                }
            }
            session(['memcache_config' => $memcache]);
            session(['redis_config' => $redis]);


            //创建配置文件
            $conf = write_config();
            session(['config_file' => $conf]);
            return $this->success('success', url('sql'));
		}
        $this->status['index']  = 'success';
        $this->status['check']  = 'success';
        $this->status['config'] = 'primary';
        $this->assign['status'] = $this->status;
        return $this->show();
	}

    /**
     * 安装数据库
     * @throws \think\Exception
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public function sql() {
        session(["error" => false]);
        if(request()->isPost()){
            try {
                //连接数据库
                $dbconfig = session('db_config');
                $db_config = [
                    'default' => $dbconfig['type'],
                    'connections' => [
                        'mysql' => [
                            'type' => $dbconfig['type'],
                            'hostname' => $dbconfig['hostname'],
                            'database' => $dbconfig['database'],
                            'username' => $dbconfig['username'],
                            'password' => $dbconfig['password'],
                            'hostport' => $dbconfig['port'],
                            'prefix' => $dbconfig['prefix']
                        ],
                    ],
                ];
                Db::setConfig($db_config);
                $db = Db::connect();
                //创建数据库
                $dbname = $dbconfig['database'];
                if(! $db->query("SHOW DATABASES LIKE '{$dbname}'")){
                    $sql = "CREATE DATABASE IF NOT EXISTS `{$dbname}` DEFAULT CHARACTER SET utf8mb4";
                    $db->execute($sql);
                }
                show_msg("数据库创建成功");

                //创建数据表
                create_tables($db, $dbconfig['prefix']);
                //注册创始人帐号
                $admin = session('admin_info');
                register_administrator($db, $dbconfig['prefix'], $admin);
                lockFile();
            }catch (\Exception $e){
                var_dump("安装异常：" . $e->getMessage());
                show_msg("安装异常：" . $e->getMessage(), 'danger');
                session(["error" => true]);
            }
            if(session('error')){
                return $this->error('数据库安装失败！');
            }
            return $this->success('数据库安装成功', url('complete'));
        }else{
            cache('install_msg', []);
            $this->status['index']  = 'success';
            $this->status['check']  = 'success';
            $this->status['config'] = 'success';
            $this->status['sql']    = 'primary';
            $this->assign['status'] = $this->status;
            return $this->show();
        }
    }

    /**
     * 轮询安装信息
     * @return \support\Response
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * Author: fudaoji<fdj@kuryun.cn>
     */
	public function getMsg(){
        if(request()->isPost()){
            $msg_list = (array)cache('install_msg');
            cache('install_msg', []);
            return $this->success('success', '', ['msg_list' => $msg_list]);
        }
    }

    /**
     * 创建完成
     * @return mixed
     * Author: fudaoji<fdj@kuryun.cn>
     */
	public function complete() {
		$this->status['index']    = 'success';
		$this->status['check']    = 'success';
		$this->status['config']   = 'success';
		$this->status['sql']      = 'success';
		$this->status['complete'] = 'primary';

		$this->assign['status'] = $this->status;
        system_reload();
		return $this->show();
	}
}