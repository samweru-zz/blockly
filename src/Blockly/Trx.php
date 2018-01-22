<?php

namespace Blockly;

class Trx{

	private $from;
	private $to;
	private $amount;

	public function __construct(string $from, string $to, string $amount){

		$this->from = $from;
		$this->to = $to;
		$this->amount = $amount;
	}

	public function getHash(){

		return \Crypt\Common\Sha::dbl256(json_encode($this->getArr()));
	}

	public function getArr(){

		return array(

			"from"=>$this->from,
			"to"=>$this->to,
			"amount"=>$this->amount
		);
	}

	public function __toString(){

		return json_encode($this->getArr());
	}
}