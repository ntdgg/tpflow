<?php
namespace app\common\model;
use think\Model;

class Role extends Model
{
    //自动验证
    protected $validate = [
        'rule' => [
            'name' => 'require|unique:role',
            'status' => 'require'
        ],
        'msg' =>
            [
                'name.require' => '角色名称必须！',
                'status.require' => '角色状态必须！',
                'name.unique' => '角色名称已经存在！'
            ]
    ];

    // 获取所有角色信息
    public function getAllRole($where = '' , $order = 'sort DESC' ,$field = '*')
    {
        return $this->field($field)->where($where)->order($order)->paginate(config('ctrl.pagenum'));
    }

    // 获取单个角色信息
    public function getRole($where = '',$field = '*')
    {
        return $this->field($field)->where($where)->find();
    }

    // 删除角色
    public function delRole($where)
    {
        if($where){
            return $this->where($where)->delete();
        }else{
            return false;
        }
    }

    // 更新角色
    public function upRole($data)
    {
        if($data) {
            return $this->data($data)->isUpdate(true)->save();
        } else {
            return false;
        }
    }
}