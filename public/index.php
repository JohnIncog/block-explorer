<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require_once dirname(__FILE__) . '/../conf/constants.php';
require_once dirname(__FILE__) . '/../bootstrap.php';
require_once dirname(__FILE__) . '/../conf/config.php';


$ips[] = '76.69.234.1';
$ips[] = '23.116.240.193';
$ips[] = '23.116.240.122';
$ips[] = '192.168.10.1';

date_default_timezone_set('UTC');

if (php_sapi_name() != "cli" && in_array($_SERVER['REMOTE_ADDR'], $config['debugbar']['allowips'])) {
	define('DEBUG_BAR', true);
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
} else {
	ini_set('display_errors', '0');
	define('DEBUG_BAR', false);
}
//
//if (php_sapi_name() != "cli" && !in_array($_SERVER['REMOTE_ADDR'], $ips)) {
//	include('../views/header.php');
//	echo '
//	<div class="my-template">
//	<div class="row">
//	<div class="col-md-3"></div>
//	<div class="col-md-6">
//		<a href="/"><img class="logo" src="/img/blockchainlogo1.png" border=""></a>
//	</div>
//
//	</div>
//	<div class="col-md-3"></div>
//	<div class="col-md-6" style="vertical-align: middle;   margin-top: 28px;">
//			<h1>Upgrading</h1>
//		</div>
//<div style="min-height: 500px"></div>
//
//	</div>
//	';
//	include('../views/footer.php');
//	exit;
//}

use lib\Bootstrap;

try {
	$app = Bootstrap::getInstance();
	$app->setConfig($config);
	$uri = false;
	if (empty($_SERVER['REQUEST_URI'])) {
		$uri = $argv[1];
	}
	$app->run($uri);
} catch (Exception $e) {
	\controllers\Home::myErrorHandler(
		E_USER_ERROR,
		"Uncaught exception 'Exception' with message '{$e->getMessage()}'",
		$e->getFile(),
		$e->getLine()
	);
}

