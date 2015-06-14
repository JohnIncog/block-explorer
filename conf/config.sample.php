<?php
$config = array(
	'site' => array(
		'name' => 'Paycoin Block Explorer',
		'contactEmails' => 'webmaster@example.com',
		'allowips' => array(
			'192.168.10.1',
		)
	),
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
		'database' => 'blockexplorer',
	),
	'memcached' => array(
		'host' => 'localhost',
		'port' => 11211
	),
	'debugbar' => array(
		'allowips' => array(
			'192.168.10.1',
		)
	)
);

return $config;
