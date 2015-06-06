<?php

namespace lib;

class Cache {

	protected static $instance;
	protected $cache;

	private function __construct() {
		include(__DIR__ . '/../conf/config.php');
		/** @var $config array */
		if (!class_exists('\\Memcached')) {
			trigger_error('Memcached not enabled', E_USER_WARNING);
			$this->cache = false;
			return;
		}
		$this->cache = new \Memcached();
		$this->cache->addServer($config['memcached']['host'], $config['memcached']['port']);
	}

	public static function getInstance() {
		if (!isset(self::$instance)) {
			self::$instance = new Cache();
		}
		return self::$instance;
	}


	public function get($key) {
		if (!$this->cache) {
			return false;
		}
		$result = $this->cache->get($key);
		//var_dump($key, $result);
		return $result;
	}

	public function set($key, $value, $expiration = null) {
		if (!$this->cache) {
			return false;
		}
		return $this->cache->set($key, $value, $expiration);
	}

}