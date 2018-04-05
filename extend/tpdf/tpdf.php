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
        'user',
    ];
    // 数据表黑名单
    private $blacklistTable = [
        'user',
    ];

    public function make($data, $option = 'all')
    {
		$this->data = $data;
        $this->module = $data['module'];
        $controllers = explode(".", $data['controller']);
        $this->name = array_pop($controllers);
        $this->nameLower = Loader::parseName($this->name);
		
		define('APP_PATH',\Env::get('app_path') );
		define('DS',DIRECTORY_SEPARATOR);
        // 检查方法是否存在
        if (isset($data['delete_file']) && $data['delete_file']) {
            $action = 'del' . ucfirst($option);
        } else {
            $action = 'build' . ucfirst($option);
        }

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
		//创建目录
		$this->buildDir();
		
		if ($action != 'buildDir') {
            // 文件路径
            $pathView = APP_PATH . $this->module . DS . "view" . DS . $this->dir . $this->nameLower . DS;
            $pathTemplate = APP_PATH . 'index' . DS . "view" . DS . "Formdesign" . DS . "template" . DS ;
            $fileName = APP_PATH . "%MODULE%" . DS . "%NAME%" . DS . $this->dir . $this->name . ".php";
            $code = $this->parseCode();
            // 执行方法
            $this->buildEdit($pathView, $pathTemplate, $fileName, $tableName, $code, $data);
        }
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
        if (in_array("resume", $menuArr) || in_array("forbid", $menuArr)) {
            $tdMenu .= tab(4) . '{$vo.status|show_status=$vo.id}' . "\n";
        }
        $tdMenu .= tab(4) . '{tp:menu menu=\'sedit\' /}' . "\n";
        // 有回收站
        if (in_array('recyclebin', $menuArr)) {
            $form = '{include file="form" /}';
            $th = '{include file="th" /}';
            $td = '{include file="td" /}';
            $tdMenu .= tab(4) . '{tp:menu menu=\'sdelete\' /}';
        } else {
            $form = implode("\n" . tab(1), $code['search']);
            $th = implode("\n" . tab(3), $code['th']);
            $td = implode("\n" . tab(3), $code['td']);
            $tdMenu .= tab(4) . '{tp:menu menu=\'sdeleteforever\' /}';
        }

        $template = file_get_contents($pathTemplate . "index.tpl");
        $file = $path . "index.html";

        //TODO 自定义模板路径
        if ($this->module == Request::module()) {
            $module = '';
        } else {
            $module = Request::module() . '@';
        }

        return file_put_contents($file, str_replace(
                ["[MODULE]", "[FORM]", "[MENU]", "[TH]", "[TD]", "[TD_MENU]", "[SCRIPT]"],
                [$module, $form, $menu, $th, $td, $tdMenu, $script],
                $template
            )
        );
    }
	 /**
     * 创建 edit.html 文件
     */
    private function buildEdit($path, $pathTemplate, $fileName, $tableName, $code, $data)
    {
		dump($code);
        $template = file_get_contents($pathTemplate . "edit.tpl");
        $file = $path . "edit.html";

        //TODO 自定义模板路径
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
        // 一定别忘记表名前缀
        $tableName = isset($this->data['table_name']) && $this->data['table_name'] ?
            $this->data['table_name'] :
            Config::get("database.prefix") . $tableName;
        // 在 MySQL 中，DROP TABLE 语句自动提交事务，因此在此事务内的任何更改都不会被回滚，不能使用事务
        // http://php.net/manual/zh/pdo.rollback.php
        $tableExist = false;
        // 判断表是否存在
        $ret = Db::query("SHOW TABLES LIKE '{$tableName}'");
        // 表存在
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
                    . (strtolower($field['default']) == 'null' ? '' : " DEFAULT '{$field['default']}'")
                    . ($field['comment'] === '' ? '' : " COMMENT '{$field['comment']}'");
            }
            // 索引
            if (isset($field['key']) && $field['key'] && $field['name'] != 'id') {
                $key[] = tab(1) . "KEY `{$field['name']}` (`{$field['name']}`)";
            }
        }

        if (isset($this->data['menu'])) {
            // 自动生成status字段，防止resume,forbid方法报错，如果不需要请到数据库自己删除
            if (in_array("resume", $this->data['menu']) || in_array("forbid", $this->data['menu'])) {
                $fieldAttr[] = tab(1) . "`status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态，1-正常 | 0-禁用'";
            }
            // 自动生成 isdelete 软删除字段，防止 delete,recycle,deleteForever 方法报错，如果不需要请到数据库自己删除
            if (in_array("delete", $this->data['menu']) || in_array("recyclebin", $this->data['menu'])) {
                // 修改官方软件删除使用记录时间戳的方式，效率较低，改为枚举类型的 tinyint(1)，相应的traits见 thinkphp/library/traits/model/FakeDelete.php，使用方法和官方一样
                // 软件删除详细介绍见：http://www.kancloud.cn/manual/thinkphp5/189658
                $fieldAttr[] = tab(1) . "`isdelete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '删除状态，1-删除 | 0-正常'";
            }
        }

        // 如果创建模型则自动生成create_time，update_time字段
        if (isset($this->data['auto_timestamp']) && $this->data['auto_timestamp']) {
            // 自动生成 create_time 字段，相应自动生成的模型也开启自动写入create_time和update_time时间，并且将类型指定为int类型
            // 时间戳使用方法见：http://www.kancloud.cn/manual/thinkphp5/138668
            $fieldAttr[] = tab(1) . "`create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间'";
            $fieldAttr[] = tab(1) . "`update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间'";
        }
		$fieldAttr[] = tab(1) . "`uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间'";
		$fieldAttr[] = tab(1) . "`add_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间'";
        // 默认自动创建主键为id
        $fieldAttr[] = tab(1) . "PRIMARY KEY (`id`)";

        // 会删除之前的表，会清空数据，重新创建表，谨慎操作
        $sql_drop = "DROP TABLE IF EXISTS `{$tableName}`";
        // 默认字符编码为utf8，表引擎默认InnoDB，其他都是默认
        $sql_create = "CREATE TABLE `{$tableName}` (\n"
            . implode(",\n", array_merge($fieldAttr, $key))
            . "\n)ENGINE=" . (isset($this->data['table_engine']) ? $this->data['table_engine'] : 'InnoDB')
            . " DEFAULT CHARSET=utf8 COMMENT '{$this->data['title']}'";

        // 写入执行的SQL到日志中，如果不是想要的表结构，请到日志中搜索BUILD_SQL，找到执行的SQL到数据库GUI软件中修改执行，修改表结构
        Log::write("BUILD_SQL：\n{$sql_drop};\n{$sql_create};", '');
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
            if (!is_dir($path)) {
                // 创建目录
                mkdir($path, 0755, true);
            }
        }
	}
	 private function parseCode()
    {
        // 是否开启排序
        $sortable = false;
        // 生成 form.html 文件的代码
        $search = ['<form class="mb-20" method="get" action="{:url($Request.action)}">'];
        // 生成 th.html 文件的代码
        $th = ['<th width="25"><input type="checkbox"></th>'];
        // 生成 td.html 文件的代码
        $td = ['<td><input type="checkbox" name="id[]" value="{$vo.id}"></td>'];
        // 生成 edit.html 文件的代码
        $editField = '<table class="table table-border table-bordered table-bg">';
        // radio类型的表单控件编辑状态使用javascript赋值
        $setChecked = [];
        // select类型的表单控件编辑状态使用javascript赋值
        $setSelected = [];
        // 搜索时被选中的值
        $searchSelected = '';
		$scriptEdit = [];
        // 控制器过滤器
        $filter = '';
		dump($this->data);
		if (isset($this->data['form']) && $this->data['form']) {
			$ii = 0;
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
				if($key % 3 == 0){
					$editField .= '<tr>';
				}
				$bulid = new build();
				$input = $bulid->convertInput($form);
				$editField .= $input['Field'];
				dump($input);
				$scriptEdit[]= $input['script_edit'];
				
				if(($key+1) % 3 == 0){
					$editField .= '</tr>';
				}
				
				
				
				
			 }
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
        // DatePicker脚本引入
        $scriptSearch = [];
       // $scriptEdit = [];
       
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
	private function tables($key)
    {
		$editField ='';
		
		return $editField;
	}
	
	 /**
     * 格式化选项值
     */
    private function parseOption($option, $string = false)
    {
        if (!$option) return ['string', $option];
        if (preg_match('/^\{\$(.*?)\}$/', $option, $match)) {
            // {$vo.item} 这种格式传入的变量
            return ['think_var', $match[1]];
        } elseif (preg_match('/^\{(.*?)\}$/', $option, $match)) {
            // {vo.item} 这种格式传入的变量
            return ['var', $match[1]];
        } else {
            if ($string) {
                return ['string', $option];
            }
            // key:val#key2:val2#val3#... 这种格式
            $ret = [];
            $arrVal = explode('#', $option);
            foreach ($arrVal as $val) {
                $keyVal = explode(':', $val, 2);
                if (count($keyVal) == 1) {
                    $ret[] = ['', $keyVal[0]];
                } else {
                    $ret[] = [$keyVal[0], $keyVal[1]];
                }
            }

            return ['array', $ret];
        }
    }
}
