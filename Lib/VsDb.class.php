<?php
class VsDb{
	public $dconnect=null;
	public function getMachineByCode($code){
		if (!$this->dconnect){
			$this->connect();
		}
		$res = $this->query('SELECT `MachineCode`,`Open` FROM `'.C('DB_EXT').'machine` WHERE `MachineCode`='.$this->escape($code).' LIMIT 1');
		$ret = mysql_fetch_assoc($res);
		$r   = new VsMachine();
		if (!$ret){
			$r->exist=False;
		}else{
			$r->exist=true;
			$r->open=$ret['Open'];
			$r->machine_code=$ret['MachineCode'];
		}
		return $r;
	}
	public function LogLogin($code,$var=array(),$tag=''){
		$ip=get_client_ip();
		$ip=serialize($ip);
		$var=serialize($var);
		$sql='INSERT INTO `'.
				C('DB_EXT').'log` '.
				'(`id`, `ip`, `code`, `var`, `tag`, `time`) VALUES '.
				'(NULL, '.
				$this->escape($ip).', '.
				(int)$code.', '.
				$this->escape($var).', '.
				$this->escape($tag).', '.
				time().')';
		$this->query($sql);
	}
	public function escape($str){
		return '\''.addslashes($str).'\'';
	}
	public function query($sql){
		if (!$this->dconnect){
			$this->connect();
		}
		return mysql_query($sql,$this->dconnect);
	}
	public function connect(){
		if (!$this->dconnect){
			$this->dconnect=mysql_connect(C('DB_HOST').':'.C('DB_PORT'),C('DB_USER'),C('DB_PASS'));
			mysql_select_db(C('DB_DB'),$this->dconnect);
		}
	}
}