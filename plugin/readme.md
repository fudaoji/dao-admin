### 应用目录结构
* app
    * admin       //业务功能逻辑主模块（名称可自定义，只要和路由定义能对应就行），至少1个模块
    * crontab       //定时任务定义
    * model     //模型，非必须
    * platform    //消息处理器模块，例如公众号消息处理、个微事件处理器等，非必须
    * queue       //消息队列  
    * service       //服务类
    * 其他模块
* config    //配置文件夹
* public      //对外开放访问入口，一般放logo和静态资源文件。此目录安装时会被移动到框架public/addons/下，文件夹名称改为对应的应用名。非必须
* vendor      //此应用依赖的三方composer包，非必须
* common.php  //应用公共函数文件，非必须
* composer.json  //composer配置文件，非必须
* Install.php  //应用安装类文件，必须
* install.sql  //应用安装SQL文件，无数据表则不要存在
* upgrade.md  //当前版本升级说明，非必须
* upgrade.sql  //升级版本的SQL文件，无内容则不要存在