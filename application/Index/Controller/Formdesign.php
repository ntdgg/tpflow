<?php
//------------------------
// 自动生成代码
//-------------------------

namespace app\index\controller;

use app\common\controller\admin;
use think\Config;
use think\Controller;
use think\Loader;
use think\Url;
use think\Db;

class Formdesign extends Admin
{
    /**
     * 首页
     * @return mixed
     */
    public function index($map=[])
    {
        if ($this->request->param("title")) $map[] = ['title','like',"%" . $this->request->param("title") . "%"];
        $list=controller('Base', 'event')->commonlist('form',$map);
		$this->assign('list', $list);
        return $this->view->fetch();
    }
	/**
     * 首页
     * @return mixed
     */
    public function desc($map=[])
    {
        
        return $this->view->fetch();
    }
	/**
     * 首页
     * @return mixed
     */
    public function add($map=[])
    {
        if ($this->request->isPost()) {
		$data = input('post.');
		$ret=controller('Base', 'event')->commonadd('form',$data);
	    if($ret['code']==0){
			return msg_return('发布成功！');
			}else{
			return msg_return($ret['data'],1);
		}
	   }
        return $this->view->fetch();
    }
	public function functions()
	{
		if ($this->request->isPost()) {
		$data = input('post.');
		$ret=controller('Base', 'event')->commonadd('form_function',$data);
	    if($ret['code']==0){
			return msg_return('发布成功！');
			}else{
			return msg_return($ret['data'],1);
		}
	   }
	   $this->assign('fid', input('id'));
        return $this->view->fetch();
		
	}
	public function ajax_sql(){
		if ($this->request->isPost()){
            $sql=input("post.sql");
			try{
			   dump(Db::query($sql));
			}catch(\Exception $e){
				return  1; 	
			}
        }
	}
    /**
     * 模拟终端
     */
    public function cmd()
    {
        echo "<p style='color: green'>代码开始生成中……</p>\n";
        $config = explode(".", $this->request->param('config', 'generate'));
        $configFile = ROOT_PATH . $config[0] . '.php';
        if (!file_exists($configFile)) {
            echo "<p style='color: red;font-weight: bold'>配置文件不存在：{$configFile}</p>\n";
            exit();
        }

        $data = include $configFile;
        $generate = new \Generate();
        $generate->run($data, $this->request->param('file', 'all'));
        echo "<p style='color: green;font-weight: bold'>代码生成成功！</p>\n";
        exit();
    }

    /**
     * 生成代码
     */
    public function run()
    {
        $generate = new \Generate();
        $data = $this->request->post();
        unset($data['file']);
        $generate->run($data, $this->request->post('file'));

        if (isset($data['delete_file']) && $data['delete_file']) {
            return ajax_return_adv('删除成功', '', false, '', '', ['action' => '']);
        }
        return ajax_return_adv('生成成功', '', false, '', '', ['action' => Url::build($data['module'] . '/' . $data['controller'] . '/index')]);
    }
}
