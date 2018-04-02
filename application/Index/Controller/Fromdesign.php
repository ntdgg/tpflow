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

class Fromdesign extends Admin
{
    /**
     * 首页
     * @return mixed
     */
    public function index($map=[])
    {
        if ($this->request->param("new_title")) $map[] = ['new_title','like',"%" . $this->request->param("new_title") . "%"];
        $list=controller('Base', 'event')->commonlist('news',$map);
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
