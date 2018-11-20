<?php
/**
 * Pms
 * 模块初始化
 * @2018年01月
 * @Gwb
 */
namespace app\common\controller;

use think\Request;
use think\Controller;
use think\facade\Config;
use think\facade\Session;

class Admin extends Controller
{
    public function initialize()
    {
		parent::initialize();
		defined('uid') or define('uid', session('uid'));
		if (null === uid) {
            $this->error('请先模拟登入！',url('index/welcome'));

            
        }
    }
}