<?php
require_cache('./function.php');
require_cache('./Lib/VsEncode.class.php');
C(require './config.php');
if (strtoupper($_SERVER['REQUEST_METHOD'])==='POST' && isset($_POST['cmd'])) {
	$cmd=getArrFromEncodedStr($_POST['cmd']);
	if (isset($cmd['cmd']) && isset($cmd['key']) && $cmd['cmd'] == 'vsauth'){
		$info=getArrFromEncodedStr($_POST['key']);
	}
}