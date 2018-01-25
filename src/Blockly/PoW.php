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

		$blockArr = $block->getArr();
		unset($blockArr["hash"]);
		unset($blockArr["transactions"]);
		$blockArr["nonce"] = $prev_nonce;

		$subject = json_encode($blockArr);//block header

		$nonce = $block->getNonce();
		$hash = $block->getHash();

		return \Crypt\Common\Sha::dbl256(sprintf("%s%s", $subject, $nonce)) == $hash; 
	}

	public function run(){

		$difficulty = $this->block->getDifficulty();
		$nonce = $this->block->getNonce();

		$blockArr = $this->block->getArr();
		unset($blockArr["transactions"]);
		unset($blockArr["hash"]);

		$subject = json_encode($blockArr);//block header

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