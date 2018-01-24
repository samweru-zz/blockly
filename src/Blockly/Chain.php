<?php

namespace Blockly;

class Chain{

	private $blocks = [];

	public function __construct(Array $chain = null){

		$prevBlock = new Block(new Data());
		$prevBlock->setHash("000a9ffff23655757777e815689bbb859a45ef667ae1bcb33b96128bda0a1eab");
		$prevBlock->setNonce(1152);

		$this->blocks[] = $prevBlock;

		if(!is_null($chain)){

			foreach($chain as $_block){

				$prevBlock = new Block(new Data($block["transactions"]), 
										$prevBlock, 
										$_block["difficulty"],
										$_block["nonce"]);

				$this->blocks[] = $prevBlock;
			}
		}
	}

	public function addBlock(Block $block){

		$this->blocks[] = $block;
	}

	public function getLastBlock(){

		return end($this->blocks);
	}

	public function getHeight(){

		return count($this->blocks);
	}

	public function getBlocks(){

		return $this->blocks;
	}

	public function getArr(){

		foreach($this->blocks as $block)
			$blocks[] = $block->getArr();

		return $blocks;
	}

	public function createBlock(Array $data){ 

		$block = new Block(new Data($data["transactions"]));
		$refl = new \ReflectionObject($block);

		foreach($data as $key=>$val){

			if(in_array($key, array("index", "timestamp", "prev_hash", "difficulty", "nonce"))){

				$property = $refl->getProperty($key);
				if($property->isPrivate())
					$property->setAccessible(true);

				$property->setValue($block, $val);
				$property->setAccessible(false);
			}
		}

		$this->blocks[] = $block;
	}

	public function __toString(){

		return json_encode($this->getArr());
	}
}