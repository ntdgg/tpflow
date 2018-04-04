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
            $this->buildIndex($pathView, $pathTemplate, $fileName, $tableName, $code, $data);
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
        $editField = '';
        // radio类型的表单控件编辑状态使用javascript赋值
        $setChecked = [];
        // select类型的表单控件编辑状态使用javascript赋值
        $setSelected = [];
        // 搜索时被选中的值
        $searchSelected = '';
        // 控制器过滤器
        $filter = '';
		if (isset($this->data['form']) && $this->data['form']) {
			
			 foreach ($this->data['form'] as $form) {
				 
				 
				 
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
				
				
			 }
		}
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
        $scriptEdit = [];
       
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
