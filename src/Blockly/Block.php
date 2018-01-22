<?php

namespace Blockly;

class Block{

	private $index = 0;
    private $timestamp;
    private $data;
    private $prev_hash;

	public function __construct(Data $data, Block $last_block = null, int $difficulty = 3, int $nonce = 1){

		$this->difficulty = $difficulty;
		$this->nonce = $nonce;

		if(!is_null($last_block)){

			$datetime = new \DateTime();
			$this->timestamp = $datetime->getTimestamp();

			$this->index = $last_block->getIndex() +1;
			$this->prev_hash = $last_block->getHash();
			$this->difficulty = $last_block->getDifficulty();
			$this->nonce = $last_block->getNonce();
		}

		$this->data = $data;
		$this->hash = "";
		
	}

	public function getIndex(){

		return $this->index;
	}

	public function setHash($hash){

		$this->hash = $hash;
	}

	public function getHash(){

		return $this->hash;
	}

	public function setNonce($nonce){

		$this->nonce = $nonce;
	}

	public function getNonce(){

		return $this->nonce;
	}

	public function getDifficulty(){

		return $this->difficulty;
	}

	public function getData(){

		return $this->data;
	}

	public function getArr(){

		return array(

			"index"=>$this->index,
			"difficulty"=>$this->difficulty,
			"timestamp"=>$this->timestamp,
			"data"=>$this->data->getArr(),
			"prev_hash"=>$this->prev_hash,
			"nonce"=>$this->nonce,
			"hash"=>$this->hash
		);
	}

	public function __toString(){

		return json_encode($this->getArr());
	}
}