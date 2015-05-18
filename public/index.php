<?php

ini_set('display_errors', '1');

require_once dirname(__FILE__) . '/../bootstrap.php';
require_once dirname(__FILE__) . '/../conf/config.php';

use PP\Bootstrap;

$app = Bootstrap::getInstance();
$app->setConfig($config);
$uri = false;
if (empty($_SERVER['REQUEST_URI'])) {
	$uri = $argv[1];
}

$app->run($uri);

