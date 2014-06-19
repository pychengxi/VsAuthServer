<?php
class VsDb{
	public $dconnect=null;
	public function getMachineByCode($code){
		if (!$this->dconnect){
			$this->connect();
		}
		$res = $this->query('SELECT * FROM `'.C('DB_EXT').'machine` WHERE `MachineCode`=\''.mysql_real_escape_string($code).'\' LIMIT 1');
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