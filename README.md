# DaoAdmin

DaoAdmin是一款基于webman的高性能中后台框架。


#### 主要功能：
- 中后台常用的管理功能
- 应用中心（APaas）
- 聚合公众号、各家小程序、企微、个微开放接口，作为一个基础的免费应用

#### 软件架构
- [webman](https://www.workerman.net/doc/webman)
- Mysql
- Redis
- [Layui](https://www.layui.com/) 
- [Layuimini](http://layuimini.99php.cn/)

#### 安装及使用文档
- 要求PHP >= 7.4
- 创建项目：
~~~shell script
composer create-project fudaoji/dao-admin  daoadmin
~~~
- 运行服务

进入daoadmin目录
~~~shell script
php start.php start -d
~~~

- 安装数据

浏览器访问 http://ip地址:8787，进入安装步骤，按界面要求操作即可。

- 进入后台

管理后台: http://ip地址:8787/admin  

#### 参与贡献

1.  Fork 本仓库
2.  新建 dev 分支
3.  提交代码
4.  新建 Pull Request

#### 交流
如果对您有帮助，麻烦star走一波，感谢！

微信交流群：

添加好友再拉入群
![输入图片说明](group.png)

#### 版权信息
[DaoAdmin] 遵循Apache2开源协议发布，并提供免费使用。

使用本框架不得用于开发违反国家有关政策的相关软件和应用，否则自行承担一切法律责任！