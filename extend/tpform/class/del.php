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

class del
{
    /**
     * 删除所有文件
     *
     * @param        $pathView
     * @param string $phpFile
     */
    public static function delAll($pathView, $phpFile = '')
    {
        try {
            $this->delTable($pathView, $phpFile);
            $this->delView($pathView, $phpFile);
            $this->delController($pathView, $phpFile);
            $this->delModel($pathView, $phpFile);
            $this->delValidate($pathView, $phpFile);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * 删除首页文件
     *
     * @param $pathView
     * @param $phpFile
     *
     * @return bool
     */
    public static function delIndex($pathView, $phpFile = '')
    {
        return $this->deleteFile($pathView . 'index.html');
    }

    /**
     * 删除form文件
     *
     * @param $pathView
     * @param $phpFile
     *
     * @return bool
     */
    public static function delForm($pathView, $phpFile = '')
    {
        return $this->deleteFile($pathView . 'form.html');
    }

    /**
     * 删除th文件
     *
     * @param $pathView
     * @param $phpFile
     *
     * @return bool
     */
    public static function delTh($pathView, $phpFile = '')
    {
        return $this->deleteFile($pathView . 'th.html');
    }

    /**
     * 删除td文件
     *
     * @param $pathView
     * @param $phpFile
     *
     * @return bool
     */
    public static function delTd($pathView, $phpFile = '')
    {
        return $this->deleteFile($pathView . 'td.html');
    }

    /**
     * 删除编辑文件
     *
     * @param $pathView
     * @param $phpFile
     *
     * @return bool
     */
    public static function delEdit($pathView, $phpFile = '')
    {
        return $this->deleteFile($pathView . 'edit.html');
    }

    /**
     * 删除回收站文件
     *
     * @param $pathView
     * @param $phpFile
     *
     * @return bool
     */
    public static function delRecycleBin($pathView, $phpFile = '')
    {
        return $this->deleteFile($pathView . 'recyclebin.html');
    }

    /**
     * 删除配置文件
     *
     * @param $pathView
     * @param $phpFile
     *
     * @return bool
     */
    public static function delConfig($pathView, $phpFile = '')
    {
        return $this->deleteFile($pathView . 'config.php');
    }

    /**
     * 删除视图文件夹
     *
     * @param $pathView
     * @param $phpFile
     *
     * @return bool
     */
    public static function delView($pathView, $phpFile = '')
    {
        return $this->deleteFile($pathView);
    }

    /**
     * 删除控制器文件
     *
     * @param $pathView
     * @param $phpFile
     *
     * @return bool
     */
    public static function delController($pathView, $phpFile = '')
    {
        $file = str_replace(
            ['%MODULE%', '%NAME%'],
            [$this->module, 'controller'],
            $phpFile
        );

        return $this->deleteFile($file);
    }

    /**
     * 删除模型文件
     *
     * @param $pathView
     * @param $phpFile
     *
     * @return bool
     */
    public static function delModel($pathView, $phpFile = '')
    {
        // 获取模型的路径，根据配置文件读取
        $module = $this->readConfig($this->module, 'app', 'model_path', Config::get('app.model_path'));
        $name = $this->parseCamelCase($this->dir) . $this->name;
        $file = APP_PATH . $module . DS . "model" . DS . $name . ".php";

        return $this->deleteFile($file);
    }

    /**
     * 删除验证器文件
     *
     * @param $pathView
     * @param $phpFile
     *
     * @return bool
     */
    public static function delValidate($pathView, $phpFile = '')
    {
        // 获取验证器的路径，根据配置文件读取
        $module = $this->readConfig($this->module, 'app', 'validate_path', Config::get('app.validate_path'));
        $file = str_replace(
            ['%MODULE%', '%NAME%'],
            [$module, 'validate'],
            $phpFile
        );

        return $this->deleteFile($file);
    }

    /**
     * 删除表
     *
     * @param        $pathView
     * @param string $phpFile
     *
     * @return bool
     */
    public static function delTable($pathView, $phpFile = '')
    {
        // 数据表表名
        $tableName = str_replace(DS, '_', $this->dir) . $this->nameLower;
        // 一定别忘记表名前缀
        $tableName = isset($this->data['table_name']) && $this->data['table_name'] ?
            $this->data['table_name'] :
            Config::get("database.prefix") . $tableName;
        // 判断表是否存在
        $ret = Db::query("SHOW TABLES LIKE '{$tableName}'");
        // 表存在
        if ($ret && isset($ret[0])) {
            // 不是强制建表但表存在时直接return
            if (!isset($this->data['create_table_force']) || !$this->data['create_table_force']) {
                return true;
            }

            // 删除表
            Db::execute("DROP TABLE IF EXISTS `{$tableName}`");
        }

        return true;
    }

    /**
     * 删除文件或目录
     *
     * @param $path
     */
    public static function deleteFile($path)
    {
        if (is_dir($path)) {
            return $this->deleteDir($path);
        } else {
            return unlink($path);
        }
    }

    /**
     * 删除目录及下面所有的文件
     *
     * @param $dir
     *
     * @return bool
     */
    public static function deleteDir($dir)
    {
        //先删除目录下的文件：
        $dh = opendir($dir);
        while ($file = readdir($dh)) {
            if ($file != "." && $file != "..") {
                $fullpath = $dir . "/" . $file;
                if (!is_dir($fullpath)) {
                    unlink($fullpath);
                } else {
                    $this->deleteDir($fullpath);
                }
            }
        }
        closedir($dh);
        //删除当前文件夹：
        if (rmdir($dir)) {
            return true;
        } else {
            return false;
        }
    }
}
