<?php
class VsEncode {
 
    public static $AUTH_KEY = 'hashtest';
 
    /**
     * 按位或运算加密字符串
     *    base64_encode((间隔位插入随机key(原文 ^ 随机key))) ^ key) = 密文
     */
    public static function encrypt($txt, $key='') {
        if(empty($key)){
            $key = self::$AUTH_KEY;
        }
        srand((double)microtime() * 1000000);
        $encrypt_key = md5(rand(0, 32000));
        $ctr = 0;
        $tmp = '';
        for($i = 0;$i < strlen($txt); $i++) {
            $ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr;
            $tmp .= $encrypt_key[$ctr] . ($txt[$i] ^ $encrypt_key[$ctr++]);
			
        }
        return base64_encode(self::passportkey($tmp, $key));
    }

    /**
     * 按位或运算解密字符串
     *    (去除间隔位的key(base64_decode(密文) ^ key)) ^ 随机key = 原文
     */
    public static function decrypt($txt, $key='') {
        if(empty($key)){
            $key = self::$AUTH_KEY;
        }
        $txt = self::passportkey(base64_decode($txt), $key);
        $tmp = '';
        for($i = 0;$i < strlen($txt); $i++) {
            $md5 = $txt[$i];
            $tmp .= $txt[++$i] ^ $md5;
        }
        return $tmp;
    }
 
    /**
     * 按位或运算间隔位处理
     */
    public static function passportkey($txt, $encrypt_key='') {
        $encrypt_key = md5($encrypt_key);
        $ctr = 0;
        $tmp = '';
        for($i = 0; $i < strlen($txt); $i++) {
            $ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr;
            $tmp .= $txt[$i] ^ $encrypt_key[$ctr++];
        }
        return $tmp;
    }
 
}
