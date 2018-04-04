<?php
/**
* 工作流类库
*/
namespace tpform;

use think\Exception;
use think\facade\Log;
use think\facade\Config;
use think\Db;
use think\Loader;
use think\facade\Request;

define ( 'FORM_MENU', realpath ( dirname ( __FILE__ ) ) );

//配置文件
require_once FORM_MENU . '/class/del.php';
require_once FORM_MENU . '/class/build.php';

class tpform
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

    public function run($data, $option = 'all')
    {
		define('APP_PATH',\Env::get('app_path') );
		define('DS',DIRECTORY_SEPARATOR);
        // 检查方法是否存在
        if (isset($data['delete_file']) && $data['delete_file']) {
            $action = 'del' . ucfirst($option);
        } else {
            $action = 'build' . ucfirst($option);
        }
        
        // 检查目录是否可写
        $pathCheck = APP_PATH . $data['module'];
        if (!self::checkWritable($pathCheck)) {
            throw new Exception("目录没有权限不可写，请执行一下命令修改权限：<br>chmod -R 755 " . realpath($pathCheck), 403);
        }
        if (isset($data['model']) && $data['model']) {
            $module = $this->readConfig($this->module, 'app', 'model_path', Config::get('app.model_path'));
            $pathCheck = APP_PATH . $module . DS;
            if (!self::checkWritable($pathCheck)) {
                throw new Exception("目录没有权限不可写，请执行一下命令修改权限：<br>chmod -R 755 " . realpath($pathCheck), 403);
            }
        }
        if (isset($data['validate']) && $data['validate']) {
            $module = $this->readConfig($this->module, 'app', 'validate_path', Config::get('app.validate_path'));
            $pathCheck = APP_PATH . $module . DS;
            if (!self::checkWritable($pathCheck)) {
                throw new Exception("目录没有权限不可写，请执行一下命令修改权限：<br>chmod -R 755 " . realpath($pathCheck), 403);
            }
        }

        // 将菜单全部转为小写
        if (isset($data['menu']) && $data['menu']) {
            foreach ($data['menu'] as &$menu) {
                $menu = strtolower($menu);
            }
        }
        $this->data = $data;
        $this->module = $data['module'];
        $controllers = explode(".", $data['controller']);
        $this->name = array_pop($controllers);
        $this->nameLower = Loader::parseName($this->name);

        // 删除刚刚生成的文件
        if (isset($data['delete_file']) && $data['delete_file']) {
            $pathView = APP_PATH . $this->module . DS . "view" . DS . $this->dir . $this->nameLower . DS;
            $fileName = APP_PATH . "%MODULE%" . DS . "%NAME%" . DS . $this->dir . $this->name . ".php";
            $this->$action($pathView, $fileName);

            return true;
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
        // 创建目录
        $dir_list = [$this->module . DS . "view" . DS . $this->dir . $this->nameLower];
        if (isset($data['model']) && $data['model']) {
            $module = $this->readConfig($this->module, 'app', 'model_path', Config::get('app.model_path'));
            $dir_list[] = $module . DS . "model";
        }
       
        if ($this->dir) {
            $dir_list[] = $this->module . DS . "controller" . DS . $this->dir;
        }
        build::buildDir($dir_list);

        if ($action != 'buildDir') {
            // 文件路径
            $pathView = APP_PATH . $this->module . DS . "view" . DS . $this->dir . $this->nameLower . DS;
            $pathTemplate = APP_PATH . 'index' . DS . "view" . DS . "Formdesign" . DS . "template" . DS ;
            $fileName = APP_PATH . "%MODULE%" . DS . "%NAME%" . DS . $this->dir . $this->name . ".php";
            $code = $this->parseCode();
            // 执行方法
            build::$action($pathView, $pathTemplate, $fileName, $tableName, $code, $data);
        }
    }

    /**
     * 检查当前模块目录是否可写
     * @return bool
     */
    public static function checkWritable($path = '')
    {
        try {
            $path = $path ? $path : APP_PATH . 'admin' . DS;
            $testFile = $path . "bulid.test";
            if (!file_put_contents($testFile, "test")) {
                return false;
            }
            // 解除锁定
            unlink($testFile);

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
	/**
     * 创建文件的代码
     * @return array
     * return [
     * 'search'          => $search,
     * 'th'              => $th,
     * 'td'              => $td,
     * 'edit'            => $editField,
     * 'set_checked'     => $setChecked,
     * 'set_selected'    => $setSelected,
     * 'search_selected' => $searchSelected,
     * 'filter'          => $filter,
     * 'validate'        => $validate,
     * ];
     */
    private function parseCode()
    {
        // 是否开启排序
        $sortable = false;
        // 生成 form.html 文件的代码
        $search = ['<form class="mb-20" method="get" action="{:\\\\think\\\\Url::build($Request.action)}">'];
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
        // 生成验证器文件的代码
        $validate = '';
        // DatePicker脚本引入
        $scriptSearch = [];
        $scriptEdit = [];
        if (isset($this->data['form']) && $this->data['form']) {
            foreach ($this->data['form'] as $form) {
                // 状态选择的自动设置为单选框
                if ($form['name'] == 'status') {
                    $form['type'] = 'radio';
                    $form['option'] = '1:启用#0:禁用';
                }
                $options = build::parseOption($form['option']);
                // 表单搜索
                if (isset($form['search']) && $form['search']) {
                    // 表单搜索
                    switch ($form['search_type']) {
                        case 'select':
                            // td
                            $td[] = '<td>{$vo.' . $form['name'] . ($form['name'] == "status" ? '|get_status' : '') . '}</td>';
                            // 默认选中
                            $searchSelected .= tab(2) . '$("[name=\'' . $form['name'] . '\']").find("[value=\'{$Request.param.' . $form['name'] . '}\']").attr("selected", true);' . "\n";
                            $search[] = tab(1) . '<div class="select-box" style="width:250px">';
                            $search[] = tab(2) . '<select name="' . $form['name'] . '" class="select">';
                            $search = array_merge($search, $this->getOption($options, $form, true, 3));
                            $search[] = tab(2) . '</select>';
                            $search[] = tab(1) . '</div>';
                            break;
                        case 'date':
                            // td
                            $td[] = '<td>{$vo.' . $form['name'] . ($form['name'] == "status" ? '|get_status' : '') . '}</td>';
                            $search[] = tab(1) . '<input type="text" class="input-text Wdate" style="width:250px" '
                                . 'placeholder="' . $form['title'] . '" name="' . $form['name'] . '" '
                                . 'value="{$Request.param.' . $form['name'] . '}" '
                                . '{literal} onfocus="WdatePicker({dateFmt:\'yyyy-MM-dd\'})" {/literal} '
                                . '>';
                            $scriptSearch['date'] = "\n" . '<script type="text/javascript" src="__LIB__/My97DatePicker/WdatePicker.js"></script>';
                            break;
                        default:
                            // td
                            if ($form['name'] == 'sort') {
                                // 排序字段特殊处理
                                $sortable = true;
                                $td[] = '<td style="padding: 0">' . "\n"
                                    . tab(1) . '<input type="number" name="sort[{$vo.id}]" value="{$vo.sort}" style="width: 60px;"' . "\n"
                                    . tab(2) . 'class="input-text text-c order-input" data-id="{$vo.id}">'
                                    . '</td>';
                            } else {
                                $td[] = '<td>{$vo.' . $form['name'] . '|high_light=$Request.param.' . $form['name'] . "}</td>";
                            }
                            $filter .= tab(2) . 'if ($this->request->param("' . $form['name'] . '")) {' . "\n"
                                . tab(3) . '$map[\'' . $form['name'] . '\'] = ["like", "%" . $this->request->param("' . $form['name'] . '") . "%"];' . "\n"
                                . tab(2) . '}' . "\n";
                            $search[] = tab(1) . '<input type="text" class="input-text" style="width:250px" '
                                . 'placeholder="' . $form['title'] . '" name="' . $form['name'] . '" '
                                . 'value="{$Request.param.' . $form['name'] . '}" '
                                . '>';
                            break;
                    }
                } else {
                    // td
                    if ($form['name'] == 'sort') {
                        // 排序字段特殊处理
                        $sortable = true;
                        $td[] = '<td style="padding: 0">' . "\n"
                            . tab(1) . '<input type="number" name="sort[{$vo.id}]" value="{$vo.sort}" style="width: 60px;"' . "\n"
                            . tab(2) . 'class="input-text text-c order-input" data-id="{$vo.id}">'
                            . '</td>';
                    } else {
                        $td[] = '<td>{$vo.' . $form['name'] . ($form['name'] == "status" ? '|get_status' : '') . '}</td>';
                    }
                }
                // th
                if (isset($form['sort']) && $form['sort']) {
                    // 带有表单排序的需使用表单排序方法
                    $th[] = '<th width="">' . "{:sort_by('{$form['title']}','{$form['name']}')}</th>";
                } else {
                    $th[] = '<th width="">' . $form['title'] . "</th>";
                }
                // 像id这种白名单字段不需要自动生成到编辑页
                if (!in_array($form['name'], ['id', 'isdelete', 'create_time', 'update_time'])) {
                    // 使用 Validform 插件前端验证数据格式，生成在表单控件上的验证规则
                    $validateForm = '';
                    if (isset($form['validate']) && $form['validate']['datatype']) {
                        $v = $form['validate'];
                        $defaultDesc = in_array($form['type'], ['checkbox', 'radio', 'select', 'date']) ? '选择' : '填写';
                        $validateForm = ' datatype="' . $v['datatype'] . '"'
                            . (' nullmsg="' . ($v['nullmsg'] ? $v['nullmsg'] : '请' . $defaultDesc . $form['title']) . '"')
                            . ($v['errormsg'] ? ' errormsg="' . $v['errormsg'] . '"' : '')
                            . (isset($form['require']) && $form['require'] ? '' : ' ignore="ignore"');
                        $validate .= tab(2) . '"' . $form['name'] . '|' . $form['title'] . '" => "'
                            . (isset($form['require']) && $form['require'] ? 'require' : '') . '",' . "\n";
                    }
                    $editField .= tab(2) . '<div class="row cl">' . "\n"
                        . tab(3) . '<label class="form-label col-xs-3 col-sm-3">'
                        . (isset($form['require']) && $form['require'] ? '<span class="c-red">*</span>' : '')
                        . $form['title'] . '：</label>' . "\n"
                        . tab(3) . '<div class="formControls col-xs-6 col-sm-6'
                        . (in_array($form['type'], ['radio', 'checkbox']) ? ' skin-minimal' : '')
                        . '">' . "\n";
                    switch ($form['type']) {
                        case "radio":
                        case "checkbox":
                            if ($form['type'] == "radio") {
                                // radio类型的控件进行编辑状态赋值，checkbox类型控件请自行根据情况赋值
                                $setChecked[] = tab(2) . '$("[name=\'' . $form['name'] . '\'][value=\'{$vo.' . $form['name'] . ' ?? \'' . $form['default'] . '\'}\']").prop("checked", true);';
                            } else {
                                $setChecked[] = tab(2) . 'var checks = \'' . $form['default'] . '\'.split(",");' . "\n"
                                    . tab(2) . 'if (checks.length > 0){' . "\n"
                                    . tab(3) . 'for (var i in checks){' . "\n"
                                    . tab(4) . '$("[name=\'' . $form['name'] . '[]\'][value=\'"+checks[i]+"\']").prop("checked", true);' . "\n"
                                    . tab(3) . '}' . "\n"
                                    . tab(2) . '}';
                            }

                            // 默认只生成一个空的示例控件，请根据情况自行复制编辑
                            $name = $form['name'] . ($form['type'] == "checkbox" ? '[]' : '');

                            switch ($options[0]) {
                                case 'string':
                                    $editField .= $this->getCheckbox($form, $name, $validateForm, $options[1], '', 0);
                                    break;
                                case 'var':
                                    $editField .= tab(4) . '{foreach name="$Think.config.conf.' . $options[1] . '" item=\'v\' key=\'k\'}' . "\n"
                                        . build::getCheckbox($form, $name, $validateForm, '{$v}', '{$k}', '{$k}')
                                        . tab(4) . '{/foreach}' . "\n";
                                    break;
                                case 'array':
                                    foreach ($options[1] as $option) {
                                        $editField .= $this->getCheckbox($form, $name, $validateForm, $option[1], $option[0], $option[0]);
                                    }
                                    break;
                            }
                            break;
                        case "select":
                            // select类型的控件进行编辑状态赋值
                            $setSelected[] = tab(2) . '$("[name=\'' . $form['name'] . '\']").find("[value=\'{$vo.' . $form['name'] . ' ?? \'' . $form['default'] . '\'}\']").attr("selected", true);';
                            $editField .= tab(4) . '<div class="select-box">' . "\n"
                                . tab(5) . '<select name="' . $form['name'] . '" class="select"' . $validateForm . '>' . "\n"
                                . implode("\n", $this->getOption($options, $form, false, 6)) . "\n"
                                . tab(5) . '</select>' . "\n"
                                . tab(4) . '</div>' . "\n";
                            break;
                        case "textarea":
                            // 默认生成的textarea加入了输入字符长度实时统计，H-ui.admin官方的textarealength方法有问题，请使用 tpadmin 框架修改后的源码，也可拷贝 H-ui.js 里相应的方法
                            // 如果不需要字符长度实时统计，请在生成代码中删除textarea上的onKeyUp事件和下面p标签那行
                            $editField .= tab(4) . '<textarea class="textarea" placeholder="" name="' . $form['name'] . '" '
                                . 'onKeyUp="textarealength(this, 100)"' . $validateForm . '>'
                                . '{$vo.' . $form['name'] . ' ?? \'' . $form['default'] . '\'}'
                                . '</textarea>' . "\n"
                                . tab(4) . '<p class="textarea-numberbar"><em class="textarea-length">0</em>/100</p>' . "\n";
                            break;
                        case "date":
                            $editField .= tab(4) . '<input type="text" class="input-text Wdate" '
                                . 'placeholder="' . $form['title'] . '" name="' . $form['name'] . '" '
                                . 'value="' . '{$vo.' . $form['name'] . ' ?? \'' . $form['default'] . '\'}' . '" '
                                . '{literal} onfocus="WdatePicker({dateFmt:\'yyyy-MM-dd\'})" {/literal} '
                                . $validateForm . '>' . "\n";
                            $scriptEdit['date'] = "\n" . '<script type="text/javascript" src="__LIB__/My97DatePicker/WdatePicker.js"></script>';
                            break;
                        case "text":
                        case "password":
                        case "number":
                        default:
                            $editField .= tab(4) . '<input type="' . $form['type'] . '" class="input-text" '
                                . 'placeholder="' . $form['title'] . '" name="' . $form['name'] . '" '
                                . 'value="' . '{$vo.' . $form['name'] . ' ?? \'' . $form['default'] . '\'}' . '" '
                                . $validateForm . '>' . "\n";
                            break;
                    }
                    $editField .= tab(3) . '</div>' . "\n"
                        . tab(3) . '<div class="col-xs-3 col-sm-3"></div>' . "\n"
                        . tab(2) . '</div>' . "\n";
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


        if ($filter) {
            $filter = 'protected function filter(&$map)' . "\n"
                . tab(1) . '{' . "\n"
                . $filter
                . tab(1) . '}';
        }
        // 自动屏蔽查询条件isdelete字段
        if (!isset($this->data['menu']) ||
            (isset($this->data['menu']) &&
                !in_array("delete", $this->data['menu']) &&
                !in_array("recyclebin", $this->data['menu'])
            )
        ) {
            $filter = 'protected static $isdelete = false;' . "\n\n" . tab(1) . $filter;
        }
        if ($validate) {
            $validate = 'protected $rule = [' . "\n" . $validate . '    ];';
        }
        // 如果没有sort字段，强制删除保存排序菜单
        if (!$sortable) {
            foreach ($this->data['menu'] as $k => $menu) {
                if ($menu == 'saveorder') {
                    unset($this->data['menu'][$k]);
                }
            }
        }

        return [
            'search'          => $search,
            'th'              => $th,
            'td'              => $td,
            'edit'            => $editField,
            'set_checked'     => $setChecked,
            'set_selected'    => $setSelected,
            'search_selected' => $searchSelected,
            'filter'          => $filter,
            'validate'        => $validate,
            'script_edit'     => $scriptEdit,
            'script_search'   => $scriptSearch,
        ];
    }

}
