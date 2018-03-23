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
		// 后台用户权限检查
		 // 用户ID
        defined('UID') or define('UID', Session::get(Config::get('rbac.user_auth_key')));
        // 是否是管理员
        defined('ADMIN') or define('ADMIN', true === Session::get(Config::get('rbac.admin_auth_key')));
        // 检查认证识别号
        if (null === UID) {
            $this->redirect(url(config('rbac.user_auth_gateway')));
        } else {
              // 用户权限检查
			if (
				Config::get('rbac.user_auth_on') &&
				!in_array($this->request->module(), explode(',', Config::get('rbac.not_auth_module')))
			) {
				if (!\Rbac::AccessDecision()) {
					$this->redirect(url('Pub/noauth'));
					exception('没有权限', 10006);
				}
			}
        }
    }
}