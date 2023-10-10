<?php
namespace tpflow\exception;

/**
 * 流程异常
 */
class FlowException extends \RuntimeException
{


    //  错误信息
    protected $error;
    //  错误代码
    protected $code;
    //  数据
    protected $data = [];

    public function __construct($error,$code,$data = [])
    {
        $this->error   = $error;
        $this->code = $code;
        $this->data = $data;
        $this->message = is_array($error) ? implode(PHP_EOL, $error) : $error;
    }

    /**
     * 获取流程错误信息
     * @access public
     * @return array|string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * 获取流程错误数据
     * @return array|string|object
     */
    public function getData()
    {
        return $this->data;
    }
}