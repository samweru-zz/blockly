<?php

namespace Blockly;

class PoW{

	private $block;

	public function __construct(Block $block){

		$this->block = $block;
	}

	private function getStopWatch(){

	    list($usec, $sec) = explode(" ", microtime());

	    return ((float)$usec + (float)$sec);
	}

	public function run(){

		$difficulty = $this->block->getDifficulty();
		$nonce = $this->block->getNonce();

		$start_time = $this->getStopWatch();

		while(true){

			$subject = json_encode($this->block->getArr());

			$hash = \Crypt\Common\Sha::dbl256(sprintf("%s%s", $subject, $nonce));

			if(substr($hash, 0, $difficulty) === str_repeat("0", $difficulty))
				break;

			$nonce++;
		}

		$end_time = $this->getStopWatch();

		$this->block->setNonce($nonce);
		$this->block->setHash($hash);

		// $duration = $end_time - $start_time;

		return $this->block;
	}
}