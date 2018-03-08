<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: huajie <banhuajie@163.com>
// +----------------------------------------------------------------------

namespace Home\Model;
use Common\Model\CommonModel;



class FlowModel extends CommonModel {

       protected $_link = array(
        'form'=>array(
            'mapping_type'  =>self::BELONGS_TO,
            'class_name'    =>'form',
            'foreign_key'   =>'form_id',
            'as_fields'     =>'form_name',
            ),

    );
   
  
    
     
}
