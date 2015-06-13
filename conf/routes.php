<?php

use Symfony\Component\Routing;


$routeCollection = new Routing\RouteCollection();

$routeCollection->add('index', new Routing\Route('/', array("class" => "\\controllers\\Explorer")));
$routeCollection->add('block', new Routing\Route('/block/{hash}', array("class" => "\\controllers\\Explorer")));
$routeCollection->add('transaction', new Routing\Route('/transaction/{txid}', array("class" => "\\controllers\\Explorer")));
$routeCollection->add('search', new Routing\Route('/search/', array("class" => "\\controllers\\Explorer")));
$routeCollection->add('address', new Routing\Route('/address/{address}', array("class" => "\\controllers\\Explorer")));
$routeCollection->add('api', new Routing\Route('/api', array("class" => "\\controllers\\Explorer")));
$routeCollection->add('about', new Routing\Route('/about', array("class" => "\\controllers\\Explorer")));
$routeCollection->add('contact', new Routing\Route('/contact', array("class" => "\\controllers\\Explorer")));
$routeCollection->add('richlist', new Routing\Route('/richlist', array("class" => "\\controllers\\Explorer")));
$routeCollection->add('primeStakes', new Routing\Route('/primestakes', array("class" => "\\controllers\\Explorer")));
$routeCollection->add('latestTransactions', new Routing\Route('/latesttransactions', array("class" => "\\controllers\\Explorer")));
$routeCollection->add('test', new Routing\Route('/test', array("class" => "\\controllers\\Explorer")));
$routeCollection->add('tagging', new Routing\Route('/tagging', array("class" => "\\controllers\\Explorer")));
$routeCollection->add('faq', new Routing\Route('/faq', array("class" => "\\controllers\\Explorer")));

$routeCollection->add('chart', new Routing\Route('/charts/{chart}', array("class" => "\\controllers\\Chart")));
$routeCollection->add('transactionsPerBlock', new Routing\Route('/charts/block/transactions', array("class" => "\\controllers\\Chart")));
$routeCollection->add('valuePerBlock', new Routing\Route('/charts/block/value', array("class" => "\\controllers\\Chart")));
$routeCollection->add('getChartData', new Routing\Route('/api/charts/{chart}', array("class" => "\\controllers\\Chart")));


$routeCollection->add('getTransaction', new Routing\Route('/api/transaction/{txid}', array("class" => "\\controllers\\Api")));
$routeCollection->add('getAddress', new Routing\Route('/api/address/{address}', array("class" => "\\controllers\\Api")));
$routeCollection->add('getRichlist', new Routing\Route('/api/richlist', array("class" => "\\controllers\\Api")));
$routeCollection->add('getPrimeStakes', new Routing\Route('/api/primestakes', array("class" => "\\controllers\\Api")));
$routeCollection->add('getBlockByHeight', new Routing\Route('/api/blockheight/{height}', array("class" => "\\controllers\\Api")));
$routeCollection->add('getBlockByHash', new Routing\Route('/api/block/{hash}', array("class" => "\\controllers\\Api")));
$routeCollection->add('getLatestBlocks', new Routing\Route('/api/latestblocks', array("class" => "\\controllers\\Api")));
$routeCollection->add('getLatestTransactions', new Routing\Route('/api/latesttransactions', array("class" => "\\controllers\\Api")));
$routeCollection->add('tagAddress', new Routing\Route('/api/tagaddress', array("class" => "\\controllers\\Api")));
$routeCollection->add('disputeAddressTag', new Routing\Route('/api/disputeaddresstag', array("class" => "\\controllers\\Api")));

$routeCollection->add('buildDatabase', new Routing\Route('/cli/buildDatabase', array("class" => "\\controllers\\Cli")));
$routeCollection->add('buildWalletDatabase', new Routing\Route('/cli/buildWalletDatabase', array("class" => "\\controllers\\Cli")));
$routeCollection->add('buildRichList', new Routing\Route('/cli/buildRichList', array("class" => "\\controllers\\Cli")));

//$routeCollection->add('video', new Routing\Route('/{slug}/video{videoId}/', array("class" => "Page_Video")));


return $routeCollection;
