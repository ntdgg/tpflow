<?php
/**
* 工作流类库
*/
namespace tpdf;

use think\Exception;
use think\facade\Log;
use think\facade\Config;
use think\Db;
use think\Loader;
use think\facade\Request;

define ( 'Tp_DF', realpath ( dirname ( __FILE__ ) ) );

require_once Tp_DF . '/class/build.php';
require_once Tp_DF . '/config/config.php';


class tpdf
{
    private $module;
    private $name;
    private $dir;
    private $namespaceSuffix;
    private $nameLower;
    private $data;
    // 控制器黑名单
    private $blacklistName = [
        'Flow','Flowdesign','Formdesign','Index','News','User'
    ];
    // 数据表黑名单
    private $blacklistTable = [
        'flow', 'flow_process','form','form_function','menu','news','news_type','role','role_user','run','run_cache','run_log','run_process','run_sign','user'
    ];

    public function make($data, $option = 'all')
    {
		$this->data = $data;
        $this->module = $data['module'];
        $this->name = ucfirst($data['controller']);
        $this->nameLower = Loader::parseName($this->name);
		define('APP_PATH',\Env::get('app_path') );
		define('DS',DIRECTORY_SEPARATOR);
		if (!self::checkPath(APP_PATH . $data['module'])) {
            throw new Exception("目录没有权限不可写!", 403);
		}
		// 将菜单全部转为小写
        if (isset($data['menu']) && $data['menu']) {
            foreach ($data['menu'] as &$menu) {
                $menu = strtolower($menu);
            }
        }
		// 数据表表名
        $tableName = str_replace(DS, '_', $this->dir) . $this->nameLower;
        // 判断是否在黑名单中
        if (in_array($data['controller'], $this->blacklistName)) {
            throw new Exception('该控制器不允许创建');
        }
        // 判断是否在数据表黑名单中
        if (isset($data['table']) && $data['table'] && in_array($tableName, $this->blacklistTable)) {
            throw new Exception('该数据表不允许创建');
        }
		$this->buildDir();//创建目录
		$pathView = APP_PATH . $this->module . DS . "view" . DS . $this->dir . $this->nameLower . DS;
        $pathTemplate = APP_PATH . 'index' . DS . "view" . DS . "formdesign" . DS . "template" . DS ;
        $fileName = APP_PATH . "%MODULE%" . DS . "%NAME%" . DS . $this->dir . $this->name . ".php";
        $code = $this->parseCode();
		if($option=='all'){
			 $this->makeAll($pathView, $pathTemplate, $fileName, $tableName, $code, $data);
		}
		if($option=='demo'){
			 $this->buildView($pathView, $pathTemplate, $fileName, $tableName, $code, $data);
		}
       
    }
	private function makeAll($pathView, $pathTemplate, $fileName, $tableName, $code, $data)
	{
		$this->buildController($pathView, $pathTemplate, $fileName, $tableName, $code, $data);
		$this->buildIndex($pathView, $pathTemplate, $fileName, $tableName, $code, $data);
		$this->buildEdit($pathView, $pathTemplate, $fileName, $tableName, $code, $data);
		$this->buildTable($pathView, $pathTemplate, $fileName, $tableName, $code, $data);
	}
	/**
     * 创建控制器文件
     */
    private function buildController($path, $pathTemplate, $fileName, $tableName, $code, $data)
    {
        $template = file_get_contents($pathTemplate . "Controller.tpl");
        $file = str_replace(
            ['%MODULE%', '%NAME%'],
            [$this->module, 'controller'],
            $fileName
        );
        return file_put_contents($file, str_replace(
                ["[MODULE]", "[TITLE]", "[NAME]", "[FILTER]", "[NAMESPACE]"],
                [$this->module, $this->data['title'], $this->name, $code['filter'], $this->namespaceSuffix],
                $template
            )
        );
    }
	 /**
     * 创建 index.html 文件
     */
    private function buildIndex($path, $pathTemplate, $fileName, $tableName, $code, $data)
    {
        $script = '';
        if ($code['search_selected']) {
            $script = '{block name="script"}' . implode("", $code['script_search']) . "\n"
                . '<script>' . "\n"
                . tab(1) . '$(function () {' . "\n"
                . $code['search_selected']
                . tab(1) . '})' . "\n"
                . '</script>' . "\n"
                . '{/block}' . "\n";
        }
        // 菜单全选的默认直接继承模板
        $menuArr = isset($this->data['menu']) ? $this->data['menu'] : [];
        $menu = '';
        if ($menuArr) {
            $menu = '{tp:menu menu="' . implode(",", $menuArr) . '" /}';
        }
        $tdMenu = '';
        $template = file_get_contents($pathTemplate . "index.tpl");
        $file = $path . "index.html";
        if ($this->module == Request::module()) {
            $module = '';
        } else {
            $module = Request::module() . '@';
        }
		if ($data['flow'] == 0) {
            $flow = "{:action('flow/btn',['wf_fid'=>\$vo.id,'wf_type'=>'".$data['table']."','status'=>\$vo.status])}";
        } else {
            $flow= '';
        }
		$form = implode("\n" . tab(1), $code['search']);
        $th = implode("\n" . tab(3), $code['th']);
        $td = implode("\n" . tab(3), $code['td']);
        $tdMenu .= tab(4) . '{tp:menu menu=\'sdeleteforever\' /}';
        return file_put_contents($file, str_replace(
                ["[MODULE]", "[FORM]", "[MENU]", "[TH]", "[TD]", "[TD_MENU]", "[SCRIPT]","[FLOW]"],
                [$module, $form, $menu, $th, $td, $tdMenu, $script,$flow],
                $template
            )
        );
    }
	/**
     * 创建 edit.html 文件
     */
    private function buildView($path, $pathTemplate, $fileName, $tableName, $code, $data)
    {
        $template = file_get_contents($pathTemplate . "view.tpl");
        $file = $path . "view.html";
        if ($this->module == Request::module() || !$this->module) {
            $module = '';
        } else {
            $module = Request::module() . '@';
        }
        return file_put_contents($file, str_replace(
            ["[MODULE]", "[ROWS]", "[SET_VALUE]", "[SCRIPT]"],
            [$module, $code['edit'], implode("\n", array_merge($code['set_checked'], $code['set_selected'])), implode("", $code['script_edit'])],
            $template));
    }
	/**
     * 创建 edit.html 文件
     */
    private function buildEdit($path, $pathTemplate, $fileName, $tableName, $code, $data)
    {
        $template = file_get_contents($pathTemplate . "edit.tpl");
        $file = $path . "edit.html";
        if ($this->module == Request::module() || !$this->module) {
            $module = '';
        } else {
            $module = Request::module() . '@';
        }
        return file_put_contents($file, str_replace(
            ["[MODULE]", "[ROWS]", "[SET_VALUE]", "[SCRIPT]"],
            [$module, $code['edit'], implode("\n", array_merge($code['set_checked'], $code['set_selected'])), implode("", $code['script_edit'])],
            $template));
    }
	/**
     * 创建数据表
     */
    private function buildTable($path, $pathTemplate, $fileName, $tableName, $code, $data)
    {
        $tableName = isset($this->data['table_name']) && $this->data['table_name'] ?
            $this->data['table_name'] : Config::get("database.prefix") . $tableName;
        $tableExist = false;// 判断表是否存在
        $ret = Db::query("SHOW TABLES LIKE '{$tableName}'");
        if ($ret && isset($ret[0])) {
            //不是强制建表但表存在时直接return
            if (!isset($this->data['create_table_force']) || !$this->data['create_table_force']) {
                return true;
            }
            Db::execute("RENAME TABLE {$tableName} to {$tableName}_build_bak");
            $tableExist = true;
        }
        $auto_create_field = ['id', 'status', 'isdelete', 'create_time', 'update_time'];
        // 强制建表和不存在原表执行建表操作
        $fieldAttr = [];
        $key = [];
        if (in_array('id', $auto_create_field)) {
            $fieldAttr[] = tab(1) . "`id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '{$this->data['title']}主键'";
        }
        foreach ($this->data['field'] as $field) {
            if (!in_array($field['name'], $auto_create_field)) {
                // 字段属性
                $fieldAttr[] = tab(1) . "`{$field['name']}` {$field['type']}"
                    . ($field['extra'] ? ' ' . $field['extra'] : '')
                    . (isset($field['not_null']) && $field['not_null'] ? ' NOT NULL' : '')
                    . (strtolower($field['default']) == null ? '' : " DEFAULT '{$field['default']}'")
                    . ($field['comment'] === '' ? '' : " COMMENT '{$field['comment']}'");
            }
            // 索引
            if (isset($field['key']) && $field['key'] && $field['name'] != 'id') {
                $key[] = tab(1) . "KEY `{$field['name']}` (`{$field['name']}`)";
            }
        }
		
        // 默认自动创建主键为id
        $fieldAttr[] = tab(1) . "PRIMARY KEY (`id`)";
		$fieldAttr[] = tab(1) . "`uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户id'";
		$fieldAttr[] = tab(1) . "`status` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '审核状态'";
		$fieldAttr[] = tab(1) . "`add_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '新增时间'";
        // 会删除之前的表，会清空数据，重新创建表，谨慎操作
		
        $sql_drop = "DROP TABLE IF EXISTS `{$tableName}`";
        // 默认字符编码为utf8，表引擎默认InnoDB，其他都是默认
        $sql_create = "CREATE TABLE `{$tableName}` (\n"
            . implode(",\n", array_merge($fieldAttr, $key))
            . "\n)ENGINE=" . (isset($this->data['table_engine']) ? $this->data['table_engine'] : 'InnoDB')
            . " DEFAULT CHARSET=utf8 COMMENT '{$this->data['title']}'";
        // execute和query方法都不支持传入分号 (;)，不支持一次执行多条 SQL
        try {
            Db::execute($sql_drop);
            Db::execute($sql_create);
            Db::execute("DROP TABLE IF EXISTS `{$tableName}_build_bak`");
        } catch (\Exception $e) {
            // 模拟事务操作，滚回原表
            if ($tableExist) {
                Db::execute("RENAME TABLE {$tableName}_build_bak to {$tableName}");
            }
            throw new Exception($e->getMessage());
        }
    }
	
	/**
	 * 目录可写检测
	 *
	 **/
	private function checkPath($path){
		try {
            $path = $path ? $path : APP_PATH . 'index' . DS;
            $testFile = $path . "bulid.test";
            if (!file_put_contents($testFile, "test")) {
                return false;
            }
            unlink($testFile);
            return true;
        } catch (Exception $e) {
            return false;
        }
	} 
	/**
	 * 目录可写检测
	 *
	 **/
	private function buildDir(){
		$dir_list = [$this->module . DS . "view" . DS . $this->dir . $this->nameLower];
        if ($this->dir) {
            $dir_list[] = $this->module . DS . "controller" . DS . $this->dir;
        }
        foreach ($dir_list as $dir) {
            $path = APP_PATH . $dir;
            if (!is_dir($path)) {// 创建目录
                mkdir($path, 0755, true);
            }
        }
	}
	 private function parseCode()
    {
        $sortable = false; // 生成 form.html 文件的代码
        $search = ['<form class="mb-20" method="get" action="{:url($Request.action)}">'];// 生成 th.html 文件的代码
        $th = ['<th width="25"><input type="checkbox"></th>'];// 生成 td.html 文件的代码
        $td = ['<td><input type="checkbox" name="id[]" value="{$vo.id}"></td>'];// 生成 edit.html 文件的代码
        $editField = '<table class="table table-border table-bordered table-bg">';// radio类型的表单控件编辑状态使用javascript赋值
        $setChecked = [];// select类型的表单控件编辑状态使用javascript赋值
        $setSelected = [];// 搜索时被选中的值
        $searchSelected = '';
		$scriptEdit = [];
        // 控制器过滤器
        $filter = '';
		if (isset($this->data['form']) && $this->data['form']) {
			$ii = 0;
			end($this->data['form']);
			$key_last = key($this->data['form']);
			 foreach ($this->data['form'] as $key =>$form) {
				/*控制器表单查询生成*/
				if(isset($form['search']) && $form['search']=='yes'){
					 $filter .= tab(2) . 'if ($this->request->param("' . $form['name'] . '")) {' . "\n"
                        . tab(3) . '$map[\'' . $form['name'] . '\'] = ["like", "%" . $this->request->param("' . $form['name'] . '") . "%"];' . "\n"
                        . tab(2) . '}' . "\n";
					 $search[] = tab(1) . '<input type="text" class="input-text" style="width:250px" '
                                . 'placeholder="' . $form['title'] . '" name="' . $form['name'] . '" '
                                . 'value="{$Request.param.' . $form['name'] . '}" '
                                . '>';
				}
				/*生成index首页用*/
				if(isset($form['lists']) && $form['lists']=='yes'){ 
					$td[] = '<td>{$vo.' . $form['name'] ."}</td>";
					$th[] = '<th>' . $form['title'] ."</th>";
				}
				
				
				/*生成 Edit.html 开始*/
				if($ii == 0){
					$editField .= "\n" . tab(3).'<tr>';
				}
				$bulid = new build();
				$input = $bulid->convertInput($form);
				$editField .= "\n" . tab(3).$input['Field'];
				$ii =$ii + $input['num'];
				if($ii == 3){
					$editField .= "\n" . tab(3).'</tr>';
					$ii = 0;
				}
				/*判断最后是否有足够的TD*/
				if($key_last == $key){
					if($ii < 3){
						$editField .= $bulid->bulidTd($ii);
					}
				}
				/*生成 Edit.html 结束*/
				$scriptEdit[]= $input['script_edit'];
			 }
		}
		if($this->data['flow']==0){ 
					$th[] = '<th>状态</th>';
					$td[] = "<td>{:action('flow/status',['status'=>\$vo.status])}</td>";
				}
		$editField .= "</table>";
		 if (count($search) > 1) {
            // 有设置搜索则显示
            $search[] = tab(1) . '<button type="submit" class="btn btn-success"><i class="Hui-iconfont">&#xe665;</i> 搜索</button>';
            $search[] = '</form>';
			} else {
				// 不设置将form.html置空
				$search = [];
		}
        $scriptSearch = [];
        return [
            'search'          => $search,
            'th'              => $th,
            'td'              => $td,
            'edit'            => $editField,
            'set_checked'     => $setChecked,
            'set_selected'    => $setSelected,
            'search_selected' => $searchSelected,
            'filter'          => $filter,
            'script_edit'     => $scriptEdit,
            'script_search'   => $scriptSearch,
        ];
    }
}
