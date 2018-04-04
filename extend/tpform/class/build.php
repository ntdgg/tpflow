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

class build
{
	
    /**
     * 生成所有文件
     */
    public static function buildAll($pathView, $pathTemplate, $fileName, $tableName, $code, $data)
    {
        // 创建文件
        self::buildIndex($pathView, $pathTemplate, $fileName, $tableName, $code, $data);
        if (isset($data['menu']) && in_array('recyclebin', $data['menu'])) {
            self::buildForm($pathView, $pathTemplate, $fileName, $tableName, $code, $data);
            self::buildTh($pathView, $pathTemplate, $fileName, $tableName, $code, $data);
            self::buildTd($pathView, $pathTemplate, $fileName, $tableName, $code, $data);
        }
        self::buildEdit($pathView, $pathTemplate, $fileName, $tableName, $code, $data);
        self::buildController($pathView, $pathTemplate, $fileName, $tableName, $code, $data);
       
        if (isset($data['create_table']) && $data['create_table']) {
            self::buildTable($pathView, $pathTemplate, $fileName, $tableName, $code, $data);
        }
      
    }


    /**
     * 创建目录
     */
    public static function buildDir($dir_list)
    {
        foreach ($dir_list as $dir) {
            $path = APP_PATH . $dir;
            if (!is_dir($path)) {
                // 创建目录
                mkdir($path, 0755, true);
            }
        }
    }

    /**
     * 创建 edit.html 文件
     */
    public static function buildEdit($path, $pathTemplate, $fileName, $tableName, $code, $data)
    {
        $template = file_get_contents($pathTemplate . "edit.tpl");
        $file = $path . "edit.html";

        //TODO 自定义模板路径
        if ($data['module'] == Request::module() || !$data['module']) {
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
     * 创建form.html文件
     */
    public static function buildForm($path, $pathTemplate, $fileName, $tableName, $code, $data)
    {
        $content = implode("\n", $code['search']);
        $file = $path . "form.html";

        return file_put_contents($file, $content);
    }

    /**
     * 创建th.html文件
     */
    public static function buildTh($path, $pathTemplate, $fileName, $tableName, $code, $data)
    {
        $content = implode("\n", $code['th']);
        $file = $path . "th.html";

        return file_put_contents($file, $content);
    }

    /**
     * 创建td.html文件
     */
    public static function buildTd($path, $pathTemplate, $fileName, $tableName, $code, $data)
    {
        $content = implode("\n", $code['td']);
        $file = $path . "td.html";

        return file_put_contents($file, $content);
    }

    /**
     * 创建 index.html 文件
     */
    public static function buildIndex($path, $pathTemplate, $fileName, $tableName, $code, $data)
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
        $menuArr = isset($data['menu']) ? $data['menu'] : [];
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
        if ($data['module'] == Request::module()) {
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
     * 创建控制器文件
     */
    public static function buildController($path, $pathTemplate, $fileName, $tableName, $code, $data)
    {
        $template = file_get_contents($pathTemplate . "Controller.tpl");
        $file = str_replace(
            ['%MODULE%', '%NAME%'],
            [$data['module'], 'controller'],
            $fileName
        );

        return file_put_contents($file, str_replace(
                ["[MODULE]", "[TITLE]", "[NAME]", "[FILTER]", "[NAMESPACE]"],
                [$data['module'], $data['title'], $data['name'], $code['filter'], $data['namespaceSuffix']],
                $template
            )
        );
    }

   

   
    /**
     * 创建数据表
     */
    public static function buildTable($path, $pathTemplate, $fileName, $tableName, $code, $data)
    {
        // 一定别忘记表名前缀
        $tableName = isset($data['table_name']) && $data['table_name'] ?
            $data['table_name'] :
            Config::get("database.prefix") . $tableName;
        // 在 MySQL 中，DROP TABLE 语句自动提交事务，因此在此事务内的任何更改都不会被回滚，不能使用事务
        // http://php.net/manual/zh/pdo.rollback.php
        $tableExist = false;
        // 判断表是否存在
        $ret = Db::query("SHOW TABLES LIKE '{$tableName}'");
        // 表存在
        if ($ret && isset($ret[0])) {
            //不是强制建表但表存在时直接return
            if (!isset($data['create_table_force']) || !$data['create_table_force']) {
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
            $fieldAttr[] = tab(1) . "`id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '{$data['title']}主键'";
        }
        foreach ($data['field'] as $field) {
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

        if (isset($data['menu'])) {
            // 自动生成status字段，防止resume,forbid方法报错，如果不需要请到数据库自己删除
            if (in_array("resume", $data['menu']) || in_array("forbid", $data['menu'])) {
                $fieldAttr[] = tab(1) . "`status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态，1-正常 | 0-禁用'";
            }
            // 自动生成 isdelete 软删除字段，防止 delete,recycle,deleteForever 方法报错，如果不需要请到数据库自己删除
            if (in_array("delete", $data['menu']) || in_array("recyclebin", $data['menu'])) {
                // 修改官方软件删除使用记录时间戳的方式，效率较低，改为枚举类型的 tinyint(1)，相应的traits见 thinkphp/library/traits/model/FakeDelete.php，使用方法和官方一样
                // 软件删除详细介绍见：http://www.kancloud.cn/manual/thinkphp5/189658
                $fieldAttr[] = tab(1) . "`isdelete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '删除状态，1-删除 | 0-正常'";
            }
        }

        // 如果创建模型则自动生成create_time，update_time字段
        if (isset($data['auto_timestamp']) && $data['auto_timestamp']) {
            // 自动生成 create_time 字段，相应自动生成的模型也开启自动写入create_time和update_time时间，并且将类型指定为int类型
            // 时间戳使用方法见：http://www.kancloud.cn/manual/thinkphp5/138668
            $fieldAttr[] = tab(1) . "`create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间'";
            $fieldAttr[] = tab(1) . "`update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间'";
        }
        // 默认自动创建主键为id
        $fieldAttr[] = tab(1) . "PRIMARY KEY (`id`)";

        // 会删除之前的表，会清空数据，重新创建表，谨慎操作
        $sql_drop = "DROP TABLE IF EXISTS `{$tableName}`";
        // 默认字符编码为utf8，表引擎默认InnoDB，其他都是默认
        $sql_create = "CREATE TABLE `{$tableName}` (\n"
            . implode(",\n", array_merge($fieldAttr, $key))
            . "\n)ENGINE=" . (isset($data['table_engine']) ? $data['table_engine'] : 'InnoDB')
            . " DEFAULT CHARSET=utf8 COMMENT '{$data['title']}'";

        // 写入执行的SQL到日志中，如果不是想要的表结构，请到日志中搜索BUILD_SQL，找到执行的SQL到数据库GUI软件中修改执行，修改表结构
        Log::write("BUILD_SQL：\n{$sql_drop};\n{$sql_create};");
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
     * 生成复选框、单选框
     */
    public static function getCheckbox($form, $name, $validateForm, $title, $value = '', $key = 0, $tab = 4)
    {
        return tab($tab) . '<div class="radio-box">' . "\n"
            . tab($tab + 1) . '<input type="' . $form['type'] . '" name="' . $name . '" '
            . 'id="' . $form['name'] . '-' . $key . '" value="' . $value . '"' . $validateForm . '>' . "\n"
            . tab($tab + 1) . '<label for="' . $form['name'] . '-' . $key . '">' . $title . '</label>' . "\n"
            . tab($tab) . '</div>' . "\n";
    }

    /**
     * 获取下拉框的option
     */
    public static function getOption($options, $form, $empty = true, $tab = 3)
    {
        switch ($options[0]) {
            case 'string':
                return [tab($tab) . '<option value="">' . $options[1] . '</option>'];
                break;
            case 'var':
                $ret = [];
                if ($empty) {
                    $ret[] = tab($tab) . '<option value="">所有' . $form['title'] . '</option>';
                }
                $ret[] = tab($tab) . '{foreach name="$Think.config.conf.' . $options[1] . '" item=\'v\' key=\'k\'}';
                $ret[] = tab($tab + 1) . '<option value="{$k}">{$v}</option>';
                $ret[] = tab($tab) . '{/foreach}';

                return $ret;
                break;
            case 'think_var':
                $ret = [];
                if ($empty) {
                    $ret[] = tab($tab) . '<option value="">所有' . $form['title'] . '</option>';
                }
                $ret[] = tab($tab) . '{foreach name="$' . $options[1] . '" item=\'v\'}';
                $ret[] = tab($tab + 1) . '<option value="{$v.id}">{$v.name}</option>';
                $ret[] = tab($tab) . '{/foreach}';

                return $ret;
                break;
            case 'array':
                $ret = [];
                foreach ($options[1] as $option) {
                    $ret[] = tab($tab) . '<option value="' . $option[0] . '">' . $option[1] . '</option>';
                }

                return $ret;
                break;
        }
    }

    /**
     * 格式化选项值
     */
    public static function parseOption($option, $string = false)
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

    /**
     * 读取配置
     *
     * @param        $module
     * @param        $scope
     * @param null   $name
     * @param string $default
     *
     * @return array|mixed|string
     */
    public static function readConfig($module, $scope, $name = null, $default = '')
    {
        // 可能的配置文件路径
        $fileConfig = APP_PATH . $module . '/config.php';
        $fileExtra = APP_PATH . $module . 'extra/' . $scope . '.php';
        $config = [];
        // 加载配置
        if (file_exists($fileExtra)) {
            $config = include $fileExtra;
        } elseif (file_exists($fileConfig)) {
            $allConfig = include $fileConfig;
            if (isset($allConfig[$scope])) {
                $config = $allConfig[$scope];
            }
        }
        // 返回值
        if ($name) {
            return isset($config[$name]) ? $config[$name] : $default;
        } else {
            return $config;
        }
    }

    /**
     * 将one/two/three转为OneTwoThree
     *
     * @param $name
     *
     * @return mixed
     */
    public static function parseCamelCase($name)
    {
        $pattern = DS == '\\' ? '/((^|\\\\)([a-z]))/' : '/((^|\\/)([a-z]))/';
        return preg_replace_callback($pattern, function ($matches) {
            return strtoupper($matches[3]);
        }, trim($name, DS));
    }
}
