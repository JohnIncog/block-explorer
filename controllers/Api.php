<?php

namespace controllers;
use PP\PaycoinDb;

class Api extends Controller {

	public function __construct($bootstrap) {
		parent::__construct($bootstrap);

	}

	public function index() {

		echo 'index';

	}

	public function getBlockByHeight() {
		$height = $this->bootstrap->route['height'];
		$paycoin = new PaycoinDb();

		$block = $paycoin->getBlockByHeight($height);
		$block['transactions'] = $paycoin->getTransactionsInBlock($block['height']);
		$block['transactionsOut'] = $paycoin->getTransactionsOut($block['height']);
		$block['raw'] = unserialize($block['raw']);
		$this->render($block);
	}



	public function getBlockByHash() {
		$hash = $this->bootstrap->route['hash'];
		$paycoin = new PaycoinDb();

		$block = $paycoin->getBlockByHash($hash);
		$block['transaction'] = $paycoin->getTransactionsInBlock($block['height']);

		$this->render($block);
	}



	public function getTransaction() {
		$txid = $this->bootstrap->route['txid'];
		$paycoin = new PaycoinDb();
		$transaction = $paycoin->getTransaction($txid);
		$transaction['raw'] = unserialize($transaction['raw']);

		$this->render($transaction);

	}


	public function getLatestBlocks() {

		$height = $this->bootstrap->httpRequest->get('height');
		$limit = $this->getLimit(10, 100);

		$paycoin = new PaycoinDb();
		$blocks = $paycoin->getLatestBlocks($limit, $height);
		foreach ($blocks as &$block) {
			$block['raw'] = unserialize($block['raw']);
		}
		$this->render($blocks);
	}

	public function getLatestTransactions() {

		$limit = $this->getLimit();

		$paycoinDb = new PaycoinDb();
		$transactions = $paycoinDb->getLatestTransactions($limit);
		$this->render($transactions);
	}


	public function getAddress() {

		$address = $this->bootstrap->route['address'];

		$limit = $this->getLimit();

		$paycoinDb = new PaycoinDb();

		$addressInformation = $paycoinDb->getAddressInformation($address, $limit);
		$this->render($addressInformation);
	}

	public function getRichlist() {

		$paycoin = new PaycoinDb();
		$richList = $paycoin->getRichList();
		$this->render($richList);

	}

	public function getPrimeStakes() {

		$limit = $this->getLimit();

		$paycoinDb = new PaycoinDb();
		$primeStakes = $paycoinDb->primeStakes($limit);
		$this->render($primeStakes);
	}

	private function getLimit($default = 100, $max = 10000) {
		$limit = $this->bootstrap->httpRequest->get('limit');
		if (!$limit) {
			$limit = $default;
		}
		if ($limit > $max) {
			$limit = $max;
		}
		return $limit;
	}

	public function render($data) {

		$cacheTime = 120;
		$ts = gmdate("D, d M Y H:i:s", time() + $cacheTime) . " GMT";
		header("Expires: $ts");
		header("Pragma: cache");
		header("Cache-Control: max-age=$cacheTime");
		header('Content-Type: application/json');

		echo json_encode(
			array(
				'version' => '0.1',
				'data' => $data
			)
		);
	}

} 