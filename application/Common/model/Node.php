<?php
/**
 * Pms
 * 节点模型
 * @2018年01月
 * @Gwb
 */

namespace app\common\model;
use think\Model;
use think\Log;

class Node extends Model
{
    //自动验证
    protected $validate = [
        'rule' => [
            'title' => 'require',
            'name' => 'require'
        ],
        'msg' =>
            [
                'title.require' => '菜单名称必须！',
                'name.require' => '节点名称必须！'
            ]
    ];

    // 获取所有节点信息
    public function getAllNode($where = '' , $order = 'sort DESC') 
	{
        return $this->where($where)->order($order)->select();
    }
    // 获取单个节点信息
    public function getNode($where = '',$field = '*') 
	{
        return $this->field($field)->where($where)->find();
    }
    // 删除节点
    public function delNode($where) 
	{
        if($where){
            return $this->where($where)->delete();
        }else{
            return false;
        }
    }
    // 更新节点
    public function upNode($data) 
	{
        if($data){
            return $this->data($data)->isUpdate(true)->save();
        }else{
            return false;
        }
    }
    // 子节点
    public function childNode($id)
	{
        return $this->where(array('pid'=>$id))->select();
    }
}