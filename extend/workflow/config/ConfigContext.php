<?php
namespace workflow;
class ConfigContext{
	private static $_instance;
	
	private $rootElement = array();
	private $nodetype = array();//当前环节名称
	private $globalVar = array(); //全局变量
	private $emailObj = array();//用户邮件处理类
	private $customObj = array();//用户信息处理类
	private $initElement = array(); //初始化脚本
	

	//private标记的构造方法
	private function __construct(){
	}
	//创建__clone方法防止对象被复制克隆
	public function __clone(){
		echo 'Clone is not allow!';
	}
	 
	//单例方法,用于访问实例的公共的静态方法
	public static function getInstance(){
		if(!(self::$_instance instanceof self)){
			self::$_instance = new self;
		}
		return self::$_instance;
	}
	//清空数据
	public function clear(){
		$this->rootElement = array();
		$this->nodetype = array();//当前环节名称
		$this->globalVar = array(); //全局变量
		$this->initElement = array(); //初始化脚本
		
	}
	//清空全局变量
	public function clearGlobalVar(){
		$this->globalVar = array(); //全局变量	
	}
	/**
	 * @return unknown
	 */
	public function getRootElement($key) {
		$value = "";
		if (array_key_exists ( $key, $this->rootElement )) {
			$value = $this->rootElement[$key];
		}
		return $value;
	}
	
	/**
	 * @param unknown_type $actionid
	 */
	public function setRootElement($key,$value) {
		$this->rootElement[$key] = $value;
	}
	/**
	 * @return unknown
	 */
	public function getGlobalVar($key) {
		$value = "";
		if (array_key_exists ( $key, $this->globalVar )) {
			$value = $this->globalVar[$key];
		}
		return $value;
	}
	
	/**
	 * @param unknown_type $globalVar
	 */
	public function setGlobalVar($key,$value) {
		$this->globalVar[$key] = $value;
	}
	
	/**
	 * @return unknown
	 */
	public function getNodetype($key) {
		$value = "";
		if (array_key_exists ( $key, $this->nodetype )) {
			$value = $this->nodetype[$key];
		}
		return $value;
	}
	
	/**
	 * @param unknown_type $nodetype
	 */
	public function setNodetype($key,$value) {
		$this->nodetype[$key] = $value;
	}
	/**
	 * @return unknown
	 */
	public function getEmailObj($name = 'default') {
		foreach ($this->emailObj as $key=>$value){
			if($key==$name){
				return $value;
			}
		}
		return false;
	}
	
	/**
	 * @param unknown_type $nodetype
	 */
	public function setEmailObj($emailArray) {
		if(!empty($emailArray)){
			foreach ($emailArray as $key => $value){
				include_once WORKFLOW_BASE . "/" .$value['path'];
				if(!empty($value['class'])){
				    $classname = 'ltworkflow\\'.$value['class'];
					$obj =  new $classname();
					if($obj instanceof InterfaceNotice){
						$this->emailObj[$key] = $obj;
					}
				}
				
			}
		}
		
	}
	/**
	 * @return unknown
	 */
	public function getCustomObj($name = 'userinfo') {
		foreach ($this->customObj as $key=>$value){
			if($key==$name){
				return $value;
			}
		}
	}
	
	/**
	 * @param unknown_type $nodetype
	 */
	public function setCustomObj($customArray) {
		foreach ($customArray as $key => $value){
			require_once WORKFLOW_BASE . "/" .$value['path'];
            $classname = 'ltworkflow\\'.$value['class'];
            $obj =  new $classname();
			$this->customObj[$key] = $obj;
		}
	}
	/**
	 * @return unknown
	 */
	public function getInitElement($key = '') {
		$value = "";
		if($key != ''){
			if (array_key_exists ( $key, $this->initElement )) {
				$value = $this->initElement[$key];
			}
		}else{
			$value = $this->initElement;
		}

		return $value;
		
	}
	
	/**
	 * @param unknown_type $initElement
	 */
	public function setInitElement($key,$value) {
		$this->initElement[$key] = $value;
	}
}