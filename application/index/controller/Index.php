<?php
// 本类由系统自动生成，仅供测试用途
namespace app\index\Controller;
use think\Controller;
use think\facade\Session;
use workflow\workflow;

class Index  extends Controller{
    public function index(){
		
		$appid = 'lt9db6fd8450b5299f';
        $secret = 'ba9f9d78629c762272d855e8219738cd';
        $curl = 'http://api.ycqpmall.com/token?appid='.$appid.'&secret='.$secret;
        $content = $this->https_request($curl);
        $access_token = json_decode($content,true)['data']['token'];
        $sign = $this->signs();
        $header = [
            'sign:'.$sign,
            'access-token:'.$access_token,
            'did:Z488py1UvAAwwTtje1amm36Qf6',
            'app-type:ios',
        ];
        $url = 'http://api.ycqpmall.com/v1/cate/744';
        $res = $this->https_request($url,null, $header);
        dump($res);
		exit;
	  $this->assign('user',db('user')->field('id,username,role')->select());
	  $this->assign('menu',db('menu')->select());
      return $this->fetch();
    }
	 public function signs(){
        $time = $this->get13TimeStamp();
        $data = [
            'did'=>'123456789',
            'app_type'=>'ios',
            'time' => $time,
       ];
       $str = $this->setSign($data);
       return $str;
    }

    public  function get13TimeStamp(){
        list($st1,$st2) = explode(' ', microtime());
        return $st2.ceil($st1*1000);
    }


    public  function setSign($data = []){
        ksort($data);
        $string = http_build_query($data);
        $string = $this->encrypt($string);
        return $string;
    }

    /**
     * 加密
     * @param String input 加密的字符串
     * @param String key   解密的key
     * @return HexString
     */
    public function encrypt($input = '') {
        $key = 'sgg45453erteret4'; //aes秘钥，服务端和客户端必须保持一致,而且aeskey的值只支持16,24,32个字符
        $size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
        $input = $this->pkcs5_pad($input, $size);
        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, '');
        $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        mcrypt_generic_init($td, $key, $iv);

        $data = mcrypt_generic($td, $input);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        $data = base64_encode($data);

        return $data;

    }

    /**
     * 填充方式 pkcs5
     * @param String text        原始字符串
     * @param String blocksize   加密长度
     * @return String
     */
    private function pkcs5_pad($text, $blocksize) {
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }
	public function welcome(){
	  $this->assign('user',db('user')->field('id,username,role')->select());
      return $this->fetch();
    }
	public function doc(){
      return $this->fetch();
    }
	public function login(){
		Session::clear();
		$info = db('user')->find(input('id'));
        Session::set('uid', $info['id']);
		Session::set('uname', $info['username']);
		Session::set('role', $info['role']);
		$this->success('模拟登入成功！');
	}
	/**
     * curl 发送请求
     * @param string url
     * @param array data
     */
    public function https_request($url, $data = null, $header = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if(!empty($header)){
            curl_setopt($curl,CURLOPT_HTTPHEADER,$header);
        }
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }
}