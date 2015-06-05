<?php
$ips[] = '76.69.234.1';
$ips[] = '23.116.240.193';
if (!in_array($_SERVER['REMOTE_ADDR'], $ips)) {
	die('<h1>UPGRADING</h1>');
}
ini_set('display_errors', '1');
//ini_set('memory_limit', '500M');
date_default_timezone_set('UTC');
require_once dirname(__FILE__) . '/../bootstrap.php';
require_once dirname(__FILE__) . '/../conf/config.php';

use lib\Bootstrap;

$app = Bootstrap::getInstance();
$app->setConfig($config);
$uri = false;
if (empty($_SERVER['REQUEST_URI'])) {
	$uri = $argv[1];
}

$app->run($uri);

