<?php
ob_start();
header('X-Powered-By: VsServer');
header('Server: VsServer');
require('./function.php');
require_cache('./Lib/VsEncode.class.php');
require_cache('./Lib/VsMachine.class.php');
require_cache('./Lib/VsDb.class.php');
require_cache('./Lib/PostError.class.php');
C(require './config.php');
$model   = new VsDb();
try {
	checkRequestMethod();
	$cmd     = getPostCmd();
	$info    = getInfoFromCmdInfo();
	checkInfo($info);
	$machine = $model->getMachineByCode($info['MachineCode']);
	if ($machine->exist() && $machine->CanLogin()){
		echo BuildSuccessResponse();
	}else{
		echo BuildFailResponse();
	}
}catch(PostError $e ){
	Display404Page();
}
