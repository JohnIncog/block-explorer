<?php
$config = array(
	'paycoind' => array(
		'rpchost' => '127.0.0.1',
		'rpcport' => 8332,
		'rpcuser' => 'user',
		'rpcpassword' => 'password',
	),
	'mysql' => array(
		'host' => 'localhost',
		'user' => 'user',
		'password' => 'password',
		'database' => 'block-explorer',
	)
);

return $config;
