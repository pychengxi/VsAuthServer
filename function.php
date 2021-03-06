<?php
define('VS_ERR_REQUEST_METHOD_NOT_ALLOWED',4);
define('VS_ERR_POST_CMDTAG_NOT_FOUND',1);
define('VS_ERR_CMD_CMDTAG_NOT_FOUND',2);
define('VS_ERR_CMD_KEYTAG_NOT_FOUND',3);
define('VS_LOGIN_SUCCESS',1001);
define('VS_LOGIN_FAIL',1002);
/**
 * 获取和设置配置参数 支持批量定义
 * @param string|array $name 配置变量
 * @param mixed $value 配置值
 * @return mixed
 */
function C($name=null, $value=null) {
    static $_config = array();
    // 无参数时获取所有
    if (empty($name)) {
        if(!empty($value) && $array = S('c_'.$value)) {
            $_config = array_merge($_config, array_change_key_case($array));
        }
        return $_config;
    }
    // 优先执行设置获取或赋值
    if (is_string($name)) {
        if (!strpos($name, '.')) {
            $name = strtolower($name);
            if (is_null($value))
                return isset($_config[$name]) ? $_config[$name] : null;
            $_config[$name] = $value;
            return;
        }
        // 二维数组设置和获取支持
        $name = explode('.', $name);
        $name[0]   =  strtolower($name[0]);
        if (is_null($value))
            return isset($_config[$name[0]][$name[1]]) ? $_config[$name[0]][$name[1]] : null;
        $_config[$name[0]][$name[1]] = $value;
        return;
    }
    // 批量设置
    if (is_array($name)){
        $_config = array_merge($_config, array_change_key_case($name));
        return;
    }
    return null; // 避免非法参数
}

/**
 * 优化的require_once
 * @param string $filename 文件地址
 * @return boolean
 */
function require_cache($filename) {
    static $_importFiles = array();
    if (!isset($_importFiles[$filename])) {
        if (file_exists($filename)) {
            require $filename;
            $_importFiles[$filename] = true;
        } else {
            $_importFiles[$filename] = false;
        }
    }
    return $_importFiles[$filename];
}

function getArrFromEncodedStr($str){
	return json_decode(VsEncode::decrypt($str),true);
}

function getPostCmd(){
	if (!isset($_POST[C('CMD_TAG')])){
		throw new PostError(VS_ERR_CMDTAG_NOT_FOUND);
	}
	return getArrFromEncodedStr($_POST[C('CMD_TAG')]);
}

function getInfoFromCmdInfo($cmdArr){
	if (!isset($cmdArr['cmd'])){
		throw new PostError(VS_ERR_CMD_CMDTAG_NOT_FOUND);
	}
	if (!isset($cmdArr['key'])){
		throw new PostError(VS_ERR_CMD_KEYTAG_NOT_FOUND);
	}
	if ($cmd['cmd'] == 'vsauth'){
		return getArrFromEncodedStr($_POST[$cmd['key']]);
	}else{
		throw new PostError(VS_ERR_CMD_CMDTAG_NOT_FOUND);
	}
}

function checkInfo($infoArr){
	return true;
}

function checkRequestMethod(){
	if (strtoupper($_SERVER['REQUEST_METHOD'])!=='POST'){
		throw new PostError(VS_ERR_REQUEST_METHOD_NOT_ALLOWED);
	}
}

function BuildSuccessResponse(){
	$arr=array();
	$arr[randString(4,4)]=mt_rand(-1000,-1);
	$arr[randString(4,4)]=mt_rand(-1000,-1);
	$arr['Fs3T']=VS_LOGIN_SUCCESS;
	$arr=shuffleAssoc($arr);
	return VsEncode::encrypt(json_encode($arr));
}

function BuildFailResponse(){
	$arr=array();
	$arr[randString(4,4)]=mt_rand(1,1000);
	$arr[randString(4,4)]=mt_rand(1,1000);
	$arr['Fs3T']=VS_LOGIN_FAIL;
	$arr=shuffleAssoc($arr);
	return VsEncode::encrypt(json_encode($arr));
}

function shuffleAssoc($array){
	$newarr=array();
	$count=count($array);
	for ($i=0;$i<$count;++$i){
		$randKey=array_rand($array);
		
		$newarr[$randKey]=$array[$randKey];
		unset($array[$randKey]);
	}
	return $newarr;
}

/**
 * 产生随机字串，可用来自动生成密码
 * 默认长度6位 字母和数字混合 支持中文
 * @param string $len 长度
 * @param string $type 字串类型
 * 0 字母 1 数字 其它 混合
 * @param string $addChars 额外字符
 * @return string
 */
function randString($len=6,$type='',$addChars='') {
	$str ='';
	switch($type) {
		case 0:
			$chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'.$addChars;
			break;
		case 1:
			$chars= str_repeat('0123456789',3);
			break;
		case 2:
			$chars='ABCDEFGHIJKLMNOPQRSTUVWXYZ'.$addChars;
			break;
		case 3:
			$chars='abcdefghijklmnopqrstuvwxyz'.$addChars;
			break;
		case 4:
			$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'.$addChars;
			break;
		default :
			// 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
			$chars='ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789'.$addChars;
			break;
	}
	if($len>10 ) {//位数过长重复字符串一定次数
		$chars= $type==1? str_repeat($chars,$len) : str_repeat($chars,5);
	}
	$chars   =   str_shuffle($chars);
	$str     =   substr($chars,0,$len);
	return $str;
}

function Display404Page(){
	ob_clean();
	header('HTTP/1.1 404 Not Found');
	header("Status: 404 Not Found");
	require '404.html';
}
/**
 * 获取客户端IP地址
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @return mixed
 */
function get_client_ip() {
	$ip=array();
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $pos    =   array_search('unknown',$arr);
        if(false !== $pos) unset($arr[$pos]);
        $ip[0]     =   trim($arr[0]);
    }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ip[1]     =   $_SERVER['HTTP_CLIENT_IP'];
    }elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip[2]     =   $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}