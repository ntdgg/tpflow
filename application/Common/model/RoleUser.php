<?php
/**
 * Pms
 * 角色节点关联
 * @2018年01月
 * @Gwb
 */
 
namespace app\common\model;
use think\Model;
use think\Log;

class RoleUser extends Model
{
    public function upRoleUser($where,$data)
    {
        if($where) {
            return $this->where($where)->update($data);
        } else {
            return false;
        }
    }

    public function addRoleUser($data)
    {
        if($data) {
            return $this->create($data);
        } else {
            return false;
        }
    }
}