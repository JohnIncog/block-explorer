<?php

namespace lib;

class RateLimiter {

	protected $key;
	protected $period;

	public function __construct($eventName, $timeInSeconds) {
		$this->key = 'ratelimit:' . $eventName;
		$this->period = $timeInSeconds;
	}

	public function allow($limit) {

		$cache = Cache::getInstance();
		$requests = $cache->get($this->key);
		$requests = (int)$requests;

		if ($requests > $limit) {
			$this->inc(2);
			return false;
		}

		$this->inc();
		return true;

	}

	public function reset() {
		$cache = Cache::getInstance();
		$cache->delete($this->key);
	}

	public function inc($offset = 1) {
		$cache = Cache::getInstance();
		$cache->increment($this->key, $offset, $this->period);
	}

	public function getTtl() {
		$cache = Cache::getInstance();
		$cache->increment($this->key, 1);
	}

}
