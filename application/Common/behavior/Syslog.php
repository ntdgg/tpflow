<?php
/**
 * Pms
 * 系统行为记录
 * @2018年01月
 * @Gwb
 */

namespace app\common\behavior;

use think\Request;

class Syslog 
{
    public function run(Request $request)
    {
		$controller = $request->controller();
		$action = $request->action();
		if(($controller=='User' && $action=='add')||($controller=='User' && $action=='edit')){
			}else{
			$log['uid']=session(config('rbac.user_auth_key'));
			$log['ip']=$request->ip();
			$log['os']=\Agent::getOs();
			$log['url']=$request->baseUrl().'|'.$request->method();
			$log['data']=json_encode($request->param());
			$log['utime']=time();
			db('sys_logs')->insertGetId($log);
		}
    }
}