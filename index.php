<?php

use Blockly\{Trx, Block, Data, Chain, PoW};
use Crypt\Common\Sha;

require("bootstrap.php");

$chain = new Chain();

$sam = sha1("sam-weru");
$dan = sha1("daniel-bedingfield");
$taxman = sha1("revenue-service");

$fee = new Trx($sam, $dan, 100);
$tax = new Trx($sam, $taxman, 10);

$data = new Data();
$data->addTrx($fee);
$data->addTrx($tax);

$last_block = $chain->getLatestBlock();

$block = new Block($data, $last_block);

$chain->addBlock($block);

//mining
foreach($chain->getBlocks() as $block){

	if(empty($block->getHash())){

		$pow = new PoW($block);
		$pow->run();
	}
}

print_r((string)$chain);