<?php
require_cache('./function.php');
require_cache('./Lib/VsEncode.class.php');
require_cache('./Lib/Db.class.php');
C(require './config.php');
try {
	checkRequestMethod();
	$cmd=getPostCmd();
	$info=getInfoFromCmdInfo();
	checkInfo($info);
	$model=new VsDb();
	$model->getMachineByCode();
}catch(PostError $e ){
	#TODO:Log Error to database
}
if ( && isset($_POST['cmd'])) {
	
	
}