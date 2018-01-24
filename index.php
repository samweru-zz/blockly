<?php

//index.php
use Blockly\{Block, Chain, Trx, Data, PoW};
use Psr\Http\Message\{RequestInterface, ResponseInterface};

require "bootstrap.php";

$cache = new \Doctrine\Common\Cache\ApcuCache();

$chain = new Chain();

if($cache->contains("chain")){

    $blocks = $cache->fetch("chain");
    array_shift($blocks);
    foreach($blocks as $block_data)
        $chain->createBlock($block_data);
}

$allowed = []; //array("user_del");

$r = new Strukt\Router\Router($servReq, $allowed);

$r->before(function(RequestInterface $req, ResponseInterface $res){

    // $path = $req->getUri()->getPath();
    //
});

$r->get("/", function(){

    return "Blockly chain.";
});

$r->get("/register/nodes", function(RequestInterface $req) use ($cache){

    $body = $req->getParsedBody();
    if(is_array($body))
        $_nodes = $body["nodes"];

    if(empty($_nodes))
        return "Found no nodes in request!";

    $nodes = [];
    if($cache->contains("nodes"))
        $nodes = $cache->fetch("nodes");

    $nodes = array_merge($nodes, $_nodes);

    $cache->save("nodes", $nodes, 21600);

    return "Nodes successfully saved";
});

$r->get("/nodes", function() use ($cache){

    if(!$cache->contains("nodes"))
        return "There are no nodes!";

    return json_encode($cache->fetch("nodes"));
});

$r->post("/add/trx", function(RequestInterface $req) use ($cache, $chain){

    $body = $req->getParsedBody();

    extract($body);

    $data = new Data();
    $data->addTrx(new Trx(sha1($sender), 
                            sha1($recipient), 
                            $amount));

    $block = new Block($data, $chain->getLastBlock());

    $chain->addBlock($block);

    $cache->save("chain", $chain->getArr(), 21600);

    return "Transaction saved successfully!";
});

$r->get("/mine", function() use ($cache, $chain){

    // print_r($chain);exit;

    $chain->mineBlocks();

    // $cache->delete("chain");

    $cache->save("chain", $chain->getArr(), 21600);

    return "Mining successful.";
});

$r->get("/chain", function() use ($chain){

    return (string)$chain;
});

$r->run();