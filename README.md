# V3.0.1 预览版本

![star](https://gitee.com/ntdgg/tpflow/badge/star.svg?theme=dark "tpflow") ![fork](https://gitee.com/ntdgg/tpflow/badge/fork.svg "tpflow") 

# TpFlow 工作流插件

> 交流群：532797225

> 对您有帮助，给我们个Star吧


> tpflow 已经走过了一个年头，我们一共发布的2个版本，为数百位开发者提供了方便，同时我们也得到了100多位朋友的支持与赞助。本开源项目将持续开源，不断的完善项目，为更多开发者提供更多的便利。

## 码云唯一开源的PHP工作流插件，欢迎加入我们，完善插件



---
> 新增众多接口，3.0正式版发布在即，欢迎提出更多建议和意见。

![markdown](http://files.git.oschina.net/group1/M00/06/3A/PaAvDFw4NRKAK6CCAAEZKRKE9TE045.png?token=cc97060f3fa5ed3cb7356ccdab6b10ae&ts=1547187474&attname=1.png "tpflow")

## 接口示例，以Thinkphp为例

```
<?php
namespace app\index\Controller;
use app\common\controller\admin;
use workflow\workflow;

class Flowdesign extends Admin {
    public function initialize()
    {
        parent::initialize();
        $this->work = new workflow();
    }
    /**
	 * 流程设计首页
	 */
   public function lists($map = []){
        $this->assign('list',$this->work->FlowApi('List'));
		$this->assign('type', ['news'=>'新闻信息','cnt'=>'合同信息','paper'=>'证件信息']);
        return  $this->fetch();
    }
.......
```
>## FlowApi 用法很简单，主要是对Flow工作流的数据封装
>详细可以阅读以下API调用方法及示例

| 参数名称  | 参数变量 |示例/说明 |
|---|---|---|
| List  |  ~~~ |$work->FlowApi('List'); //直接获取到工作流列表数据 |
| AddFlow  |  $work->FlowApi('AddFlow',$data) | $data //POST数据 |
| EditFlow|  $work->FlowApi('EditFlow',$data); |$data //POST数据 |
| GetFlowInfo|  $work->FlowApi('GetFlowInfo',input('id')) |$id 为Flow组件 |



>## ProcessApi用法很简单，主要是对工作流的步骤进行封装
>详细可以阅读以下API调用方法及示例

| 参数名称  | 参数变量 |示例/说明 |
|---|---|---|
| All| $flow_id 流程主键 |$this->work->ProcessApi('All',$flow_id); //获取对应流程所有步骤信息，返回JSON json_encode(['total'=>$process_total,'list'=>$process_data]
| ProcessDel|  $this->work->ProcessApi('ProcessDel',$flow_id,$process_id) | $process_id $flow_id 返回Array 
| ProcessDelAll| $this->work->ProcessApi('ProcessDelAll',$flow_id); |$flow_id  清空所有步骤
| ProcessLink|  $this->work->ProcessApi('ProcessLink',$flow_id,$process_info) |保存设计 |
| ProcessAttView|  $this->work->ProcessApi('ProcessAttView',input('id')) |查看步骤设置 |
| ProcessAttSave|  $this->work->ProcessApi('ProcessAttSave',$data['process_id'],$data) |保存步骤信息 |

---

> 官方博客：http://www.cojz8.com/

> 官方博客：http://tpflow.cojz8.com/   

> 工作流手册：https://www.kancloud.cn/guowenbin/tpflow

> 视频教程：http://www.cojz8.com/article/86

---


