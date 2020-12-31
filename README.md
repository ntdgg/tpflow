# Tpflow V5.0 正式版

**欢迎使用 Tpflow 工作流引擎**

*   TpFlow工作流引擎是一套规范化的流程管理系统，基于业务而驱动系统生命力的一套引擎。 
*   彻底释放整个信息管理系统的的活力，让系统更具可用性，智能应用型，便捷设计性。 
*   Tpflow团队致力于打造中国最优秀的PHP工作流引擎。

![star](https://gitee.com/ntdgg/tpflow/badge/star.svg?theme=gvp "tpflow") ![fork](https://gitee.com/ntdgg/tpflow/badge/fork.svg?theme=gvp "tpflow") 


### 主要特性

+ 基于  `<jsPlumb>` 可视化设计流程图
    + 支持可视化界面设计
    + 支持拖拽式流程绘制
    + 三布局便捷调整
    + 基于`workflow.5.0.js` `workflow.5.0.css ` 引擎
+ 超级强大的API 对接功能
    + `WfDo` 工作流直API接口
    + `designapi` 工作流设计器API接口
    + `wfapi ` 工作流管理API接口
    + `wfAccess ` 静态调用API接口
+ 完善的流引擎机制
    + 规范的命名空间，可拓展的集成化开发
    + 支持 直线式、会签式、转出式、同步审批式等多格式的工作流格式
    + 支持自定义事务驱动
    + 支持各种ORM接口
    + 业务驱动接口
+ 提供基于 `Thinkphp6.0.X` 的样例Demo
+ 提供完整的设计手册


>5.0 有的新特性及功能

*   基于`<Entrust>`驱动的代理模式管理模块
    * 可以随心调用工作流管理模式
    * 可以代理工作流的审核审批人员
*  `<LoadClass>` 支持自定义的业务驱动模式
    * 业务办理前，办理后的的各种业务流程处理
*  全新的工作流设计界面  `步骤更清晰` `设计更简单`
    * 独立化步骤显示
    * TAB式步骤属性配置
    * 步骤审批、步骤模式更加清晰
 *  环形审批流模式
    * 解决以往A发起人->B审核人->C核准人->A发起人完结 的环型审批流 

### 安装使用简易教程
 *  安装Composer
 *  composer require guoguo/tpflow
 *  复制assets/work到项目资源目录
 *  修改src/tpflow/config/common.php的配置文件

### 在线文档

[看云文档](https://www.kancloud.cn/guowenbin/tpflow "安装手册")   [官方博客](https://www.cojz8.com/ "官方博客")

### 界面截图

![markdown](https://img.kancloud.cn/42/7a/427adc1dcc2ff3ffb52087b1cfde346b_1366x622.png)



~~~
设计器、源代码标注有版权的位置，未经许可，严禁删除及修改，违者将承担法律侵权责任！
~~~
### 相关链接
---

> 官方博客：https://www.cojz8.com/

> 演示站点：http://tpflow.cojz8.com/   

> 工作流手册：https://www.kancloud.cn/guowenbin/tpflow  赞助用户【VIP群】1062040103

> 视频教程：https://www.kancloud.cn/guowenbin/tpflow_video 【付费】视频教程

---

## 版权信息

Tpflow 遵循 MIT 开源协议发布，并提供免费使用。

本项目包含的第三方源码和二进制文件之版权信息另行标注。

版权所有Copyright © 2018-2020 by Tpflow (http://cojz8.com)

All rights reserved。

~~~
对您有帮助的话，你可以在下方赞助我们，让我们更好的维护开发，谢谢！
特别声明：坚决打击网络诈骗行为，严禁将本插件集成在任何违法违规的程序上。
~~~
