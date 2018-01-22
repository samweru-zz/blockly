<?php

namespace Blockly;

class Chain{

	private $blocks = [];

	public function __construct(){

		$block = new Block(new Data());
		$block->setHash("000a9ffff23655757777e815689bbb859a45ef667ae1bcb33b96128bda0a1eab");
		$block->setNonce(1152);

		$this->blocks[] = $block;
	}

	public function addBlock(Block $block){

		$this->blocks[] = $block;
	}

	public function getLatestBlock(){

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

	public function __toString(){

		return json_encode($this->getArr());
	}
}