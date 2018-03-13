<?php /*a:1:{s:58:"D:\tpflow\application/index/view\flowdesign\attribute.html";i:1520861909;}*/ ?>

<html>
 <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="author" content="leipi.org">
    
    <!--[if lte IE 6]>
    <link rel="stylesheet" type="text/css" href="/static/work/css/bootstrap/css/bootstrap-ie6.css?">
    <![endif]-->
    <!--[if lte IE 7]>
    <link rel="stylesheet" type="text/css" href="/static/work/css/bootstrap/css/ie.css?">
    <![endif]-->
    <link href="/static/work/css/site.css?" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="/static/work/js/flowdesign/flowdesign.css"/>
<link rel="stylesheet" type="text/css" href="/static/work/js/jquery.multiselect2side/css/jquery.multiselect2side.css"/>
<link href="/static/work/css/bootstrap/css/bootstrap.css?2025" rel="stylesheet" type="text/css" />
<ul class="nav nav-tabs" id="attributeTab">
  <li <?php if($op == 'basic'): ?> class="active"<?php endif; ?>><a href="#attrBasic">常规</a></li>
  <li <?php if($op == 'form'): ?> class="active"<?php endif; ?>><a href="#attrForm">表单</a></li>
  <li><a href="#attrPower">权限</a></li>
  <li><a href="#attrOperate">操作</a></li>
  <li <?php if($op == 'judge'): ?> class="active"<?php endif; ?> id="tab_attrJudge"><a href="#attrJudge">转出条件</a></li>
  <li <?php if($op == 'style'): ?> class="active"<?php endif; ?>><a href="#attrStyle">样式</a></li>
</ul>

<form class="form-horizontal" target="hiddeniframe" method="post" id="flow_attribute" name="flow_attribute" action="<?php echo url('save_attribute'); ?>">
<input type="hidden" name="flow_id" value="<?php echo htmlentities($one['flow_id']); ?>"/>
<input type="hidden" name="process_id" value="<?php echo htmlentities($one['id']); ?>"/>
  <div class="tab-content">
    <div class="tab-pane <?php if($op == 'basic'): ?>active<?php endif; ?>" id="attrBasic">

          <div class="control-group">
            <label class="control-label" for="process_name">步骤名称</label>
            <div class="controls">
              <input type="text" id="process_name" placeholder="步骤名称" name="process_name" value="<?php echo htmlentities($one['process_name']); ?>">
            </div>
          </div>

          <div class="control-group">
            <label class="control-label">步骤类型</label>
            <div class="controls">
              <label class="radio inline">
                <input type="radio" name="process_type" value="is_step" <?php if($one['process_type'] == 'is_step'): ?>checked="checked"<?php endif; ?>>正常步骤
              </label>
              <label class="radio inline">
                <input type="radio" name="process_type" value="is_child" <?php if($one['process_type'] == 'is_child'): ?>checked="checked"<?php endif; ?>>转入子流程
              </label>
              <label class="radio inline">
                <input type="radio" name="process_type" value="is_one" <?php if($one['process_type'] == 'is_one'): ?>checked="checked"<?php endif; ?>>设为第一步
              </label>

            </div>
          </div>
<hr/>

        <div id="current_flow">
          <div class="offset1">
          <!--未按顺序的bug 2012-12-12-->
            <select multiple="multiple" size="6" name="process_to[]" id="process_multiple" >
            <?php if(is_array($process_to_list) || $process_to_list instanceof \think\Collection || $process_to_list instanceof \think\Paginator): $i = 0; $__LIST__ = $process_to_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;if($vo['id'] != $one['id']): ?>
                    <option value="<?php echo htmlentities($vo['id']); ?>" <?php if(in_array($vo['id'],$one['process_to'])): ?>selected="selected"<?php endif; ?>><?php echo htmlentities($vo['process_name']); ?></option>
                <?php endif; endforeach; endif; else: echo "" ;endif; ?>
            </select>
          </div>
        </div><!-- current_flow end -->

        <div id="child_flow" class="hide">
           <div class="control-group">
            <label class="control-label" >子流程</label>
            <div class="controls">
              <select name="child_id" >
                <option value="0">--请选择--</option>
                <?php if(is_array($child_flow_list) || $child_flow_list instanceof \think\Collection || $child_flow_list instanceof \think\Paginator): $i = 0; $__LIST__ = $child_flow_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                    <option value="<?php echo htmlentities($vo['id']); ?>" <?php if($vo['id'] == $one['child_id']): ?>selected="selected"<?php endif; ?>><?php echo htmlentities($vo['flow_name']); ?></option>
               <?php endforeach; endif; else: echo "" ;endif; ?>
              </select>
            </div>
          </div>

           <div class="control-group">
            <label class="control-label" >子流程结束后动作</label>
            <div class="controls">
              <label class="radio inline">
                <input type="radio" name="child_after" value="1" <?php if($one['child_after'] == 1): ?>checked="checked"<?php endif; ?>>
                同时结束父流程
              </label>
              <label class="radio inline">
                <input type="radio" name="child_after" value="2"  <?php if($one['child_after'] == 2): ?>checked="checked"<?php endif; ?>>
                返回父流程步骤
              </label>
            </div>
          </div>

          <div class="control-group hide" id="child_back_id">
            <label class="control-label" >返回步骤</label>
            <div class="controls">
              <select name="child_back_process" >
                <option value="0">--默认--</option>
                <?php if(is_array($process_to_list) || $process_to_list instanceof \think\Collection || $process_to_list instanceof \think\Paginator): $i = 0; $__LIST__ = $process_to_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                  <option value="<?php echo htmlentities($vo['id']); ?>" ><?php echo htmlentities($vo['process_name']); ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
                <!--option value="1">步骤1</option>
                <option value="2">步骤2</option>
                <option value="3">步骤3</option-->
              </select>
              <span class="help-inline">默认为当前步骤下一步</span>
            </div>
          </div>

        </div><!-- child_flow end -->
 </div>
<!-- attrBasic end -->
    <div class="tab-pane <?php if($op == 'form'): ?>active<?php endif; ?>" id="attrForm">



<table class="table table-condensed table-bordered table-hover" >
    <tr>
      <th style="text-align:center">字段名称</th>
      <th style="text-align:center">控件类型</th>
      <!--
        预留功能：
        1.是否必填
        2.锁定内容不能修改
      -->
      <th style="width:100px;"><label title="本步骤可写字段"  class="checkbox"><input type="checkbox" id="write">可写字段</label></th>
      <th style="width:100px;"><label title="保密字段对于本步骤主办人、经办人均为不可见"  class="checkbox"><input type="checkbox" id="secret">保密字段</label></th>
    </tr>
    <tbody>

<!-- 这里是表单设计器的字段 start -->

<!--tr>
  <th>姓名</th>
  <td>文本框</td>
  <td><label style="display: block;" ><input type="checkbox" name="write[]" checked="true" value="data_2" key="1" id="write_1"></label></td>
  <td><label style="display: block;"><input type="checkbox" name="secret[]" disabled="true" value="data_2" key="1" id="secret_1"></label></td>
</tr-->
<!-- 这里是表单设计器的字段 end -->


    </tbody>
</table>



    </div><!-- attrForm end -->
    <div class="tab-pane" id="attrPower">

        <div class="control-group">
            <label class="control-label" >自动选人</label>
            <div class="controls">
              <select name="auto_person" id="auto_person_id">
                <option value="0">不自动选人</option>
               <!-- <option value="1" <?php if($one['auto_person'] == 1): ?>selected="selected"<?php endif; ?>>发起人</option>
                <option value="2" <?php if($one['auto_person'] == 2): ?>selected="selected"<?php endif; ?>>发起人的部门主管</option>
                <option value="3" <?php if($one['auto_person'] == 3): ?>selected="selected"<?php endif; ?>>处理人的部门主管</option> -->
                <option value="5" <?php if($one['auto_person'] == 5): ?>selected="selected"<?php endif; ?>>指定角色</option>
                <option value="4" <?php if($one['auto_person'] == 4): ?>selected="selected"<?php endif; ?>>指定人员</option>
              </select>
              <span class="help-inline">预先设置自动选人，更方便转交工作</span>
            </div>
            <div class="controls <?php if($one['auto_person'] == 0): ?>hide<?php endif; ?>" id="auto_unlock_id" >
              <label class="checkbox">
                <input type="checkbox" name="auto_unlock" value="1" <?php if($one['auto_unlock'] == 1): ?>checked="checked"<?php endif; ?>>允许更改
              </label>
            </div>

            <div id="auto_person_4" <?php if($one['auto_person'] != 4): ?>class="hide"<?php endif; ?>>
              <div class="control-group">
                <label class="control-label">指定主办人</label>
                <div class="controls">
                    <input type="hidden" name="auto_sponsor_ids" id="auto_sponsor_ids" value="<?php echo htmlentities($one['auto_sponsor_ids']); ?>">
                    <input class="input-xlarge" readonly="readonly" type="text" placeholder="指定主办人" name="auto_sponsor_text" id="auto_sponsor_text" value="<?php echo isset($one['auto_sponsor_text']) ? htmlentities($one['auto_sponsor_text']) : ''; ?>"> <a href="javascript:void(0);" class="btn" onclick="superDialog('<?php echo url('/index/demo/super_dialog',['op'=>'user']); ?>','auto_sponsor_text','auto_sponsor_ids');">选择</a>
                </div> 
              </div>
              <div class="control-group">
                <label class="control-label">指定经办人</label>
                <div class="controls">
                    <input type="hidden" name="auto_respon_ids" id="auto_respon_ids" value="<?php echo htmlentities($one['auto_respon_ids']); ?>">
                    <input class="input-xlarge" readonly="readonly" type="text" placeholder="指定经办人" name="auto_respon_text" id="auto_userop_text" value="<?php echo isset($one['auto_userop_text']) ? htmlentities($one['auto_userop_text']) : ''; ?>"> <a href="javascript:void(0);" class="btn" onclick="superDialog('<?php echo url('/index/demo/super_dialog',['op'=>'user']); ?>','auto_userop_text','auto_respon_ids');">选择</a>
                </div> 
              </div>
            </div>
            <div id="auto_person_5" <?php if($one['auto_person'] != 5): ?>class="hide"<?php endif; ?>>
              <div class="control-group">
                <label class="control-label">指定角色</label>
                <div class="controls">
                    <input type="hidden" name="auto_role_ids" id="auto_role_value" value="<?php echo isset($one['auto_role']) ? htmlentities($one['auto_role']) : ''; ?>">
                    <input class="input-xlarge" readonly="readonly" type="text" placeholder="指定角色" name="auto_role_text" id="auto_role_text" value="<?php echo isset($one['auto_role_text']) ? htmlentities($one['auto_role_text']) : ''; ?>"> <a href="javascript:void(0);" class="btn" onclick="superDialog('<?php echo url('/index/demo/super_dialog',['op'=>'role']); ?>','auto_role_text','auto_role_value');">选择</a>
                </div> 
              </div>
            </div>

          </div>
<hr/>
<h4>授权范围</h4>
          <div class="control-group">
            <label class="control-label">授权人员</label>
            <div class="controls">
                <input type="hidden" name="range_user_ids" id="range_user_ids" value="<?php echo htmlentities($one['range_user_ids']); ?>">
                <input class="input-xlarge" readonly="readonly" type="text" placeholder="选择人员" name="range_user_text" id="range_user_text" value="<?php echo htmlentities($one['range_user_text']); ?>"> <a href="javascript:void(0);" class="btn" onclick="layer_show('授权人员',<?php echo url('/demo/super_dialog/op/user'); ?>','range_user_text','range_user_ids');">选择</a>
            </div> 
          </div>

          <div class="control-group">
            <label class="control-label">授权部门</label>
            <div class="controls">
                <input type="hidden" name="range_dept_ids" id="range_dept_ids" value="<?php echo htmlentities($one['range_dept_ids']); ?>">
                <input class="input-xlarge" readonly="readonly" type="text" placeholder="选择部门" name="range_dept_text" id="range_dept_text" value="<?php echo htmlentities($one['range_dept_text']); ?>"> <a href="javascript:void(0);" class="btn" onclick="superDialog('<?php echo url('/demo/super_dialog/op/dept'); ?>','range_dept_text','range_dept_ids');">选择</a>
            </div> 
          </div>

          <div class="control-group">
            <label class="control-label">授权角色</label>
            <div class="controls">
                <input type="hidden" name="range_role_ids" id="range_role_ids" value="<?php echo htmlentities($one['range_role_ids']); ?>">
                <input class="input-xlarge" readonly="readonly" type="text" placeholder="选择角色" name="range_role_text" id="range_role_text" value="<?php echo htmlentities($one['range_role_text']); ?>"> <a href="javascript:void(0);" class="btn" onclick="superDialog('<?php echo url('/demo/super_dialog/op/role'); ?>','range_role_text','range_role_ids');">选择</a>
            </div> 
          </div>


          <div class="control-group">
            <div class="controls">
                <span class="help-block">当需要手动选人时，则授权范围生效</span>
            </div> 
          </div>
          


    </div><!-- attrPower end -->
    <div class="tab-pane" id="attrOperate">

        <div class="control-group">
          <label class="control-label" >交接方式</label>
          <div class="controls">
            <select name="receive_type" >
              <option value="0" <?php if($one['receive_type'] == 0): ?>selected="selected"<?php endif; ?>>明确指定主办人</option>
              <option value="1" <?php if($one['receive_type'] == 1): ?>selected="selected"<?php endif; ?>>先接收为主办人</option>
            </select>
          </div>
        </div>

        <div class="control-group">
          <div class="controls">
            <label class="checkbox">
                <input type="checkbox" name="is_user_end" value="1" <?php if($one['is_user_end'] == 1): ?>checked="checked"<?php endif; ?>>允许主办人办结流程(最后步骤默认允许)
              </label>
            <label class="checkbox">
                <input type="checkbox" name="is_userop_pass" value="1" <?php if($one['is_userop_pass'] == 1): ?>checked="checked"<?php endif; ?>>经办人可以转交下一步
              </label>
          </div>
        </div>
<hr/>

        <div class="control-group">
          <label class="control-label" >会签方式</label>
          <div class="controls">
            <select name="is_sing" >
              <option value="1" <?php if($one['is_sing'] == 1): ?>selected="selected"<?php endif; ?>>允许会签</option>
              <option value="2" <?php if($one['is_sing'] == 2): ?>selected="selected"<?php endif; ?>>禁止会签</option>
              <option value="3" <?php if($one['is_sing'] == 3): ?>selected="selected"<?php endif; ?>>强制会签</option>
            </select>
            <span class="help-inline">如果设置强制会签，则本步骤全部人都会签后才能转交或办结</span>
          </div>
        </div>

        <div class="control-group">
          <label class="control-label" >可见性</label>
          <div class="controls">
            <select name="sign_look" >
              <option value="1" <?php if($one['sign_look'] == 1): ?>selected="selected"<?php endif; ?>>总是可见</option>
              <option value="2" <?php if($one['sign_look'] == 2): ?>selected="selected"<?php endif; ?>>本步骤之间经办人不可见</option>
              <option value="3" <?php if($one['sign_look'] == 3): ?>selected="selected"<?php endif; ?>>其它步骤不可见</option>
            </select>
          </div>
        </div>


<hr/>

        <div class="control-group">
          <label class="control-label" >回退方式</label>
          <div class="controls">
            <select name="is_back" >
              <option value="1" <?php if($one['is_back'] == 1): ?>selected="selected"<?php endif; ?>>不允许</option>
              <option value="2" <?php if($one['is_back'] == 2): ?>selected="selected"<?php endif; ?>>允许回退上一步</option>
              <option value="3" <?php if($one['is_back'] == 3): ?>selected="selected"<?php endif; ?>>允许回退之前步骤</option>
            </select>
          </div>
        </div>


    </div><!-- attrOperate end -->
    <div class="tab-pane  <?php if($op == 'judge'): ?>active<?php endif; ?>" id="attrJudge">

       
    <table class="table" >
      <thead>
        <tr>
          <th style="width:100px;">转出步骤</th>
          <th>转出条件设置</th>
        </tr>
      </thead>
      <tbody>

<!--模板-->
<tr id="tpl" class="hide">    
<td style="width: 100px;">@text</td>
<td>
    <table class="table table-condensed">
    <tbody>
      <tr>
        <td>
            <select id="field_@a" class="input-medium">
              <option value="">选择字段</option>
              <!-- 表单字段 start -->
             
              <!-- 表单字段 end -->  
            </select>
            <select id="condition_@a" class="input-small">
        <option value="=">等于</option>
        <option value="&lt;&gt;">不等于</option>
        <option value="&gt;">大于</option>
        <option value="&lt;">小于</option>
        <option value="&gt;=">大于等于</option>
        <option value="&lt;=">小于等于</option>
        <option value="include">包含</option>
        <option value="exclude">不包含</option>
            </select>
            <input type="text" id="item_value_@a" class="input-small">
            <select id="relation_@a" class="input-small">
        <option value="AND">与</option>
        <option value="OR">或者</option>
            </select>
        </td>
        <td>
            <div class="btn-group">
        <button type="button" class="btn btn-small" onclick="fnAddLeftParenthesis('@a')">（</button>
        <button type="button" class="btn btn-small" onclick="fnAddRightParenthesis('@a')">）</button>
        <button type="button" onclick="fnAddConditions('@a')" class="btn btn-small">新增</button>
            </div>
        </td>
       </tr>
       <tr>
        <td>
            <select id="conList_@a" multiple="" style="width: 100%;height: 80px;"></select>
        </td>
        <td>
            <div class="btn-group">
        <button type="button" onclick="fnDelCon('@a')" class="btn btn-small">删行</button>
        <button type="button" onclick="fnClearCon('@a')" class="btn btn-small">清空</button>
            </div>
        </td>
      </tr>
      <tr>
        <td>
            <input id="process_in_desc_@a" type="text" name="process_in_desc_@a" style="width:98%;">
            <input name="process_in_set_@a" id="process_in_set_@a" type="hidden">
        </td>
        <td>
            <span class="xc1">不符合条件时的提示</span>
        </td>
      </tr>
    </tbody>
    </table>
</td>
</tr>


  </tbody>
  <tbody id="ctbody">

  </tbody>
</table>
<input type="hidden" name="process_condition" id="process_condition">






    </div><!-- attrJudge end -->
    <div class="tab-pane  <?php if($op == 'style'): ?>active<?php endif; ?>" id="attrStyle">

        <div class="control-group">
          <label class="control-label" for="process_name">尺寸</label>
          <div class="controls">
            <input type="text" class="input-small" name="style_width" id="style_width" placeholder="宽度PX" value="<?php echo htmlentities($one['style']['width']); ?>"> X <input type="text" class="input-small" name="style_height" id="style_height" placeholder="高度PX"  value="<?php echo htmlentities($one['style']['height']); ?>">
          </div>
        </div>

        <div class="control-group">
          <label class="control-label" for="process_name">字体颜色</label>
          <div class="controls">
            <input type="text" class="input-small" name="style_color" id="style_color" placeholder="#000000" value="<?php echo htmlentities($one['style']['color']); ?>">
            <div class="colors" org-bind="style_color">
                <ul>
                  <li class="Black active" org-data="#000" title="Black">1</li>
                  <li class="red" org-data="#d54e21" title="Red">2</li>
                  <li class="green" org-data="#78a300" title="Green">3</li>
                  <li class="blue" org-data="#0e76a8" title="Blue">4</li>
                  <li class="aero" org-data="#9cc2cb" title="Aero">5</li>
                  <li class="grey" org-data="#73716e" title="Grey">6</li>
                  <li class="orange" org-data="#f70" title="Orange">7</li>
                  <li class="yellow" org-data="#fc0" title="Yellow">8</li>
                  <li class="pink" org-data="#ff66b5" title="Pink">9</li>
                  <li class="purple" org-data="#6a5a8c" title="Purple">10</li>
                </ul>
            </div>

          </div>
        </div>

 

        <div class="control-group">
          <label class="control-label" for="process_name"><span class="process-flag badge badge-inverse"><i class="icon-star-empty icon-white" id="style_icon_preview"></i></span> 图标</label>
          <div class="controls">
            <input type="text" class="input-medium" name="style_icon" id="style_icon" placeholder="icon" value="<?php echo htmlentities($one['style']['icon']); ?>">
            <div class="colors" org-bind="style_icon">
                <ul>
                  <li class="Black active" org-data="icon-star-empty" title="Black"><i class="icon-star-empty icon-white"></i></li>
                  <li class="red" org-data="icon-ok" title="Red"><i class="icon-ok icon-white"></i></li>
                  <li class="green" org-data="icon-remove" title="Green"><i class="icon-remove icon-white"></i></li>
                  <li class="blue" org-data="icon-refresh" title="Blue"><i class="icon-refresh icon-white"></i></li>
                  <li class="aero" org-data="icon-plane" title="Aero"><i class="icon-plane icon-white"></i></li>
                  <li class="grey" org-data="icon-play" title="Grey"><i class="icon-play icon-white"></i></li>
                  <li class="orange" org-data="icon-heart" title="Orange"><i class="icon-heart icon-white"></i></li>
                  <li class="yellow" org-data="icon-random" title="Yellow"><i class="icon-random icon-white"></i></li>
                  <li class="pink" org-data="icon-home" title="Pink"><i class="icon-home icon-white"></i></li>
                  <li class="purple" org-data="icon-lock" title="Purple"><i class="icon-lock icon-white"></i></li>
                </ul>
                <a href="http://v2.bootcss.com/base-css.html#icons" target="_blank">更多</a>
            </div>
          </div>
        </div>
        

        <div class="control-group">
          <label class="control-label"></label>
          <div class="controls">
            <span class="help-inline">想要更多设置，可以自行添加，或反馈给我们 [ 雷劈网 ]。</span>
          </div>
        </div>
        
        <!-- 不太完善，隐藏
         <div class="control-group">
          <label class="control-label">CSS3 图形</label>
          <div class="controls">
            <select name="style_graph" id="style_graph">
              <option value="">矩形</option>
              <option value="circle">圆形</option>
              <option value="oval">椭圆</option>
              <option value="hexagon">菱形</option>
            </select>
            <span class="help-inline">CSS3仅支持部分浏览器</span>
          </div>
        </div> -->


    </div><!-- attrStyle end -->
  </div>


<div>
  <hr/>
  <span class="pull-right">
      <a href="#" class="btn" data-dismiss="modal" aria-hidden="true">取消</a>
      <button class="btn btn-primary" type="submit" id="attributeOK">确定保存</button>
  </span>
</div>
</form>
<iframe id="hiddeniframe" style="display: none;" name="hiddeniframe"></iframe>



<script type="text/javascript">

var _out_condition_data = [];
function callbackSuperDialog(selectValue){
     var aResult = selectValue.split('@leipi@');
     $('#'+window._viewField).val(aResult[0]);
     $('#'+window._hidField).val(aResult[1]);
    //document.getElementById(window._hidField).value = aResult[1];
    
}
/**
 * 弹出窗选择用户部门角色
 * showModalDialog 方式选择用户
 * URL 选择器地址
 * viewField 用来显示数据的ID
 * hidField 隐藏域数据ID
 * isOnly 是否只能选一条数据
 * dialogWidth * dialogHeight 弹出的窗口大小
 */
function superDialog(URL,viewField,hidField,isOnly,dialogWidth,dialogHeight)
{
    dialogWidth || (dialogWidth = 620)
    ,dialogHeight || (dialogHeight = 520)
    ,loc_x = 500
    ,loc_y = 40
    ,window._viewField = viewField
    ,window._hidField= hidField;
    // loc_x = document.body.scrollLeft+event.clientX-event.offsetX;
    //loc_y = document.body.scrollTop+event.clientY-event.offsetY;
    if(window.ActiveXObject){ //IE  
        var selectValue = window.showModalDialog(URL,self,"edge:raised;scroll:1;status:0;help:0;resizable:1;dialogWidth:"+dialogWidth+"px;dialogHeight:"+dialogHeight+"px;dialogTop:"+loc_y+"px;dialogLeft:"+loc_x+"px");
        if(selectValue){
            callbackSuperDialog(selectValue);
        }
    }else{  //非IE 
        var selectValue = window.open(URL, 'newwindow','height='+dialogHeight+',width='+dialogWidth+',top='+loc_y+',left='+loc_x+',toolbar=no,menubar=no,scrollbars=no, resizable=no,location=no, status=no');  
    
    }
}
</script>

<script type="text/javascript" src="/static/work/js/jquery-1.7.2.min.js?"></script>
<script type="text/javascript" src="/static/work/css/bootstrap/js/bootstrap.min.js?"></script>
<script type="text/javascript" src="/static/work/js/jquery-ui/jquery-ui-1.9.2-min.js?" ></script>
<script type="text/javascript" src="/static/work/js/jsPlumb/jquery.jsPlumb-1.3.16-all-min.js?"></script>
<script type="text/javascript" src="/static/work/js/jquery.contextmenu.r2.js?"></script>
<!--select 2-->
<script type="text/javascript" src="/static/work/js/jquery.multiselect2side/js/jquery.multiselect2side.js?" ></script>
<!--flowdesign-->
<script type="text/javascript" src="/static/work/js/flowdesign/leipi.flowdesign.v3.js?"></script>
<script type="text/javascript" src="/static/work/js/flowdesign/attribute.js"></script>
