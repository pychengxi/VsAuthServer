<?php
class PostError extends Exception{
	public function __construct($code){
		parent::__construct('');
		$this->code=$code;
	}
}