<?php

use Symfony\Component\Routing;


$routeCollection = new Routing\RouteCollection();

$routeCollection->add('index', new Routing\Route('/', array("class" => "\\controllers\\Explorer")));
$routeCollection->add('block', new Routing\Route('/block/{hash}', array("class" => "\\controllers\\Explorer")));
$routeCollection->add('transaction', new Routing\Route('/transaction/{txid}', array("class" => "\\controllers\\Explorer")));
$routeCollection->add('search', new Routing\Route('/search/{q}', array("class" => "\\controllers\\Explorer")));
$routeCollection->add('address', new Routing\Route('/address/{address}', array("class" => "\\controllers\\Explorer")));


$routeCollection->add('test', new Routing\Route('/test', array("class" => "\\controllers\\Home")));

$routeCollection->add('getTransaction', new Routing\Route('/api/transaction/{txid}', array("class" => "\\controllers\\Api")));
$routeCollection->add('getBlockByHeight', new Routing\Route('/api/block/{height}', array("class" => "\\controllers\\Api")));
$routeCollection->add('getBlockByHash', new Routing\Route('/api/blockhash/{hash}', array("class" => "\\controllers\\Api")));
$routeCollection->add('getLatestBlocks', new Routing\Route('/api/latestblocks', array("class" => "\\controllers\\Api")));

$routeCollection->add('buildDatabase', new Routing\Route('/cli/buildDatabase', array("class" => "\\controllers\\Cli")));

//$routeCollection->add('video', new Routing\Route('/{slug}/video{videoId}/', array("class" => "Page_Video")));


return $routeCollection;
