# DaoAdmin

DaoAdmin是一款基于webman的高性能中后台框架。


#### 主要功能：
- 中后台常用的管理功能
- 应用中心（APaas）
- 聚合微信开放接口，作为一个基础的免费应用
- 聚合抖音开放接口，作为一个基础的免费应用
- 聚合支付宝开放接口，作为一个基础的免费应用

#### 完成部分:
- [x] 权限管理
- [x] 数据库scheme管理
- [x] 客户管理
    - [x] 客户列表
    - [x] 客户应用
- [x] 应用管理
    - [x] 应用列表
    - [x] 应用分类
- [x] 免费应用
    - [x] 应用demo
    - [ ] 微信平台(公众号、小程序、个微、小商店)  
    - [ ] 抖音平台(小程序) 
    - [ ] 支付宝平台(小程序)    

#### 体验账号
- 运营后台：http://daoadmin.oudewa.cn/admin  (账号：daoadmin  密码：123456)
- 商户后台；http://daoadmin.oudewa.cn/tenant  (账号：daoadmin  密码：123456)

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

![输入图片说明](wx_group.png)

#### 版权信息
[DaoAdmin] 遵循Apache2开源协议发布，并提供免费使用。

使用本框架不得用于开发违反国家有关政策的相关软件和应用，否则自行承担一切法律责任！