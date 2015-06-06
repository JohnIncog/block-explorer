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
		return $result;
	}

	public function delete($key) {
		if (!$this->cache) {
			return false;
		}
		$result = $this->cache->delete($key);
		return $result;
	}


	public function increment($key, $offset, $ttl = 0) {
		if (!$this->cache) {
			return false;
		}
		$result = $this->cache->increment($key, $offset);
		if ($result == false) {
			$this->cache->set($key, 1, $ttl);
		}

		return $result;
	}

	public function setTTL($key, $ttl) {
		if (!$this->cache) {
			return false;
		}
		$result = $this->cache->touch($key, $ttl);
		return $result;
	}

	public function set($key, $value, $expiration = null) {
		if (!$this->cache) {
			return false;
		}
		return $this->cache->set($key, $value, $expiration);
	}

}