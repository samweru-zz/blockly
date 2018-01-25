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

	public static function validate(Block $block, $prev_nonce){

		$block_data = $block->getArr();
		$block_data["hash"] = "";
		$block_data["nonce"] = $prev_nonce;

		$subject = json_encode($block_data);

		$nonce = $block->getNonce();
		$hash = $block->getHash();

		// print_r(array(\Crypt\Common\Sha::dbl256(sprintf("%s%s", $subject, $nonce)), $hash));

		return \Crypt\Common\Sha::dbl256(sprintf("%s%s", $subject, $nonce)) == $hash; 
	}

	public function run(){

		$difficulty = $this->block->getDifficulty();
		$nonce = $this->block->getNonce();
		$subject = json_encode($this->block->getArr());

		$start_time = $this->getStopWatch();

		while(true){

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