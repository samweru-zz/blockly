<?php

namespace Blockly;

class Data{

	private $proof;
	private $trxs;
	private $tree;
	private $merkleTree;

	public function __construct(Array $trxs = null){

		$hash = function($data){

			return \Crypt\Common\Sha::dbl256($data);
		};

		$this->merkleTree = new \Merkle\Tree($hash);

		$this->trxs = [];

		if(!is_null($trxs))
			foreach($trxs as $trx)
				$this->addTrx(new Trx($trx["from"], $trx["to"], $trx["amount"]));
	}

	public function addTrx(Trx $trx){

		$this->trxs[] = $trx;

		$this->tree = $this->merkleTree->add(new \Merkle\Leaf($trx->getArr()));
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