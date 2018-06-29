<?php

use Blockly\{Block, Chain, Trx, Data, PoW};
use Psr\Http\Message\{RequestInterface, ResponseInterface};
use Zend\Http\Client;

require "bootstrap.php";

$difficulty = 3; //three preceding zeros on hash

$cache = new \Doctrine\Common\Cache\ApcuCache();

$chain = new Chain();
if($cache->contains("chain")){

    $chainArr = $cache->fetch("chain");
    $chain = new Chain($chainArr);
}

$r = new Strukt\Router\Router($servReq);

$r->get("/", function(){

    return "Blockly chain.";
});

$r->get("/register/nodes", function(RequestInterface $req) use ($cache){

    $nodesTmp = [];
    $body = json_decode(str_replace("'", '"', trim((string)$req->getParsedBody())), 1);

    if(is_array($body))
        if(!empty($body))
            $nodesTmp = $body["nodes"];

    if(empty($nodesTmp))
        return "Found no nodes in request!";

    $nodes = [];
    if($cache->contains("nodes"))
        $nodes = $cache->fetch("nodes");

    if(!empty($nodes))
        $nodes = array_diff($nodes, $nodesTmp);

    $nodes = array_merge($nodesTmp, $nodes);

    $cache->save("nodes", $nodes, 21600);

    return "Nodes successfully saved";
});

$r->get("/nodes", function() use ($cache){

    if(!$cache->contains("nodes"))
        return "There are no nodes!";

    return json_encode($cache->fetch("nodes"));
});

$r->post("/add/trx", function(RequestInterface $req) use ($cache, $chain, $difficulty){

    $sender = $req->getAttribute("sender");
    $receipient = $req->getAttribute("receipient");
    $amount = $req->getAttribute("amount");

    $data = new Data();
    $data->addTrx(new Trx(sha1($sender), 
                            sha1($recipient), 
                            $amount));

    $block = new Block($data, $chain->getLastBlock(), $difficulty);

    $chain->addBlock($block);

    $cache->save("chain", $chain->getArr(), 21600);

    return "Transaction saved successfully!";
});

$r->get("/mine", function() use ($cache, $chain){

    $chain->mineBlocks();

    $cache->save("chain", $chain->getArr(), 21600);

    return "Mining successful.";
});

$r->get("/consensus", function(RequestInterface $req) use ($cache, $chain){

    $servParam = $req->getServerParams();

    $message = "Our chain rules them all!";

    $nodes = $cache->fetch("nodes");
    if(empty($nodes))
        return "There no nodes available!";

    $ourBlocks = $chain->getBlocks();

    foreach($nodes as $node){

        if($node == $servParam["HTTP_HOST"])
            continue;

        $client = new Client();
        $client->setUri(sprintf('http://%s/chain', $node));
        $client->setMethod('GET');
        $client->setOptions(array(

            'maxredirects' => 0,
            'timeout' => 1200
        ));
        
        $response = $client->send();

        $body = $response->getBody();

        $notOurBlocksArr = json_decode($body, 1);

        $notOurChain = new Chain($notOurBlocksArr);

        $notOurBlocks = $notOurChain->getBlocks();
        $ourBlocks = $chain->getBlocks();

        $isProofOfWorkValid = true;
        $isOtherChainGreater = false;

        //check length of other chain compare to ours
        if(count($notOurBlocks) > count($ourBlocks)){

            $isOtherChainGreater = true;

            $prevBlock = array_shift($notOurBlocks);

            foreach($notOurBlocks as $block){

                if(!PoW::validate($block, $prevBlock->getNonce())){
                    
                    $isProofOfWorkValid = false;
                }
            }
        }

        $isOtherChainTrxValid = true;

        // validate other chains transactions
        foreach($notOurBlocks as $block){

            $merkleTree = Chain::newMerkleTree();

            $data = $block->getData();
            $arrData = $data->getArr();

            foreach($arrData as $trx)
                $tree = $merkleTree->add(new Merkle\Leaf($trx));

            $mTree = $data->getMerkleTree();
            if(!is_null($mTree))
                if(key($mTree)!=key($tree)){

                    $isOtherChainTrxValid = false;
                    break;
                }
        }

        if($isOtherChainTrxValid && $isProofOfWorkValid && $isOtherChainGreater){

            $cache->save("chain", $notOurChain->getArr());
            $message = "Our chain has been replaced!";
        }
    }

    return $message;
});
    
$r->get("/chain", function() use ($chain){

    return (string)$chain;
});

$r->run();