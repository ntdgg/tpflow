<?php
/**
*+------------------
* 工作流任务服务
*+------------------ 
*/

namespace tpdf;

class Build{
	/**
	  * input 转换
	  */
	public function convertInput($form)
	{
		$editField='';
		$script_edit = '';
		switch ($form['type']) {
			case "radio":
			case "checkbox":
			if ($form['type'] == "radio") {
					$editField .= '<td>'.$form['title'].'</td><td><div class="skin-minimal">';
					foreach($form['option']["options"] as $v){
						$editField .= '<div class="radio-box" ><input type="radio"   name="'.$form['name'].'"   value='.$v['label'].'><label for="radio-0">'.$v['label'].'</label></div>';
					}
					$editField .= '</div></td>';
				}
				break;
			case "select":
				$editField .= '<td>'.$form['title'].'</td><td><span class="select-box"><select name="'.$form['name'].'"  class="select"  datatype="*">';
					foreach($form['option']["options"] as $v){
						$editField .= '<option value="'.$v['label'].'" >'.$v['label'].'</option>	';
					}
				$editField .= '</select></td>';
				break;
			case "textarea":
				
			case "date":
				$editField .= '<td>'.$form['title'].'</td><td><input type="text" class="input-text date" value="{$info.' . $form['name'] . ' ?? \'\'}" name="'.$form['name'].'" datatype="*"></td>';
				$script_edit .= "laydate.render({elem: '.date'});";
				break;
			case "text":
			case "password":
			case "number":
			default:
				$editField .= '<td>'.$form['title'].'</td><td><input type="' .$form['type'] . '" class="input-text" '
				. 'placeholder="' . $form['title'] . '" name="' . $form['name']. '" '
				. 'value="' . '{$vo.' . $form['name'] . ' ?? \'' . $form['default'] . '\'}' . '">' . "\n </td>";
			break;
		}
		return ['Field'=>$editField,'script_edit'=>$script_edit];
		
	}
}