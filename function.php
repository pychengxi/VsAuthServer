<?php
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
        if (file_exists_case($filename)) {
            require $filename;
            $_importFiles[$filename] = true;
        } else {
            $_importFiles[$filename] = false;
        }
    }
    return $_importFiles[$filename];
}

function getArrFromEncodedStr($str){
	return json_decode(VsEncode::decode($str),true);
}
define('VS_ERR_REQUEST_METHOD_NOT_ALLOWED',0);
define('VS_ERR_POST_CMDTAG_NOT_FOUND',1);
define('VS_ERR_CMD_CMDTAG_NOT_FOUND',2);
define('VS_ERR_CMD_KEYTAG_NOT_FOUND',3);
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