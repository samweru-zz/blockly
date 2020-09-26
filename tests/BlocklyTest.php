<?php

use Blockly\{Trx, Block, Data, Chain, PoW};
use Crypt\Common\Sha;

class BlocklyTest extends PHPUnit\Framework\TestCase{

	public function setUp():void{

		$this->chain = new Chain();

		$sam = sha1("samweru");
		$dan = sha1("daniel-bedingfield");
		$taxman = sha1("revenue-service");
		 
		$fee = new Trx($sam, $dan, 100);
		$tax = new Trx($sam, $taxman, 10);
		 
		$data = new Data();
		$data->addTrx($fee);
		$data->addTrx($tax);
		 
		$last_block = $this->chain->getLastBlock();

		// $difficulty = $last_block->getDifficulty();
		$difficulty = 3;

		$nonce = $last_block->getNonce();
		 
		$block = new Block($data, $last_block, $difficulty);
		 
		$this->chain->addBlock($block);
	}

	public function testMining(){
		 
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

	public function testMerkleTreeValidation(){

		//validate all blockchain transactions
		
		// print_r($this->chain->getArr());

		foreach($this->chain->getBlocks() as $block){

			$merkleTree = Chain::newMerkleTree();

			$data = $block->getData();
			$arrData = $data->getArr();

			foreach($arrData as $trx)
				$tree = $merkleTree->add(new Merkle\Leaf($trx));

			$mTree = $data->getMerkleTree();
			if(!is_null($mTree))
				$this->assertTrue(key($mTree) == key($tree));
				// print_r(array($mTree, $tree));
		}
	}
}