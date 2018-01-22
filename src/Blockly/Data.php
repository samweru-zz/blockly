<?php

namespace Blockly;

class Data{

	private $proof;
	private $trxs;
	private $tree;

	public function __construct(){

		$hash = function($data){

			return \Crypt\Common\Sha::dbl256($data);
		};

		$this->trxs = [];
		$this->tree = new \Merkle\Tree($hash);
	}

	public function addTrx(Trx $trx){

		$this->trxs[] = $trx;

		$this->tree->add(new \Merkle\Leaf($trx->getArr()));
	}

	public function getMerkleTree(){

		return $this->tree;
	}

	public function getTransactions(){

		return $this->trxs;
	}

	public function getArr(){

		$trxs = [];
		foreach($this->trxs as $trx)
			$trxs[$trx->getHash()] = $trx->getArr();

		return $trxs;
	}

	public function __toString(){

		return json_encode($this->getArr());
	}
}