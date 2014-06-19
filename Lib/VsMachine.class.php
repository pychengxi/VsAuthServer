<?php
class VsMachine{
	public $exist=False;
	public $machine_code='';
	public $open=0;
	public function exist(){
		return $this->exist;
	}
	public function CanLogin(){
		return $this->exist() && $this->open==1;
	}
}