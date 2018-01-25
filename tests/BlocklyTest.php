<?php

use Blockly\{Trx, Block, Data, Chain, PoW};
use Crypt\Common\Sha;

class BlocklyTest extends PHPUnit_Framework_TestCase{

	public function setUp(){

		$this->chain = new Chain();
	}

	public function testRun(){
 
		$sam = sha1("samweru");
		$dan = sha1("daniel-bedingfield");
		$taxman = sha1("revenue-service");
		 
		$fee = new Trx($sam, $dan, 100);
		$tax = new Trx($sam, $taxman, 10);
		 
		$data = new Data();
		$data->addTrx($fee);
		$data->addTrx($tax);
		 
		$last_block = $this->chain->getLastBlock();
		$difficulty = $last_block->getDifficulty();
		$nonce = $last_block->getNonce();
		 
		$block = new Block($data, $last_block);
		 
		$this->chain->addBlock($block);
		 
		//mining
		foreach($this->chain->getBlocks() as $block){

		 	$other_blocks[] = $block;

		 	if(empty($block->getHash())){

		 		$prevBlock = current($other_blocks);
		 
		 		$pow = new PoW($block);
		 		$blocks[] = $pow->run();

		 		$last_nonce = $prevBlock->getNonce();

		 		break;
		 	}
		}

		$block = reset($blocks);
		$this->assertTrue(count($blocks) == 1);
		$this->assertTrue(!empty($block->getHash()));
		$this->assertTrue(PoW::validate($block, $last_nonce));
	}
}