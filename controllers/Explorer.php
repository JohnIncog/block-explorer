<?php

namespace controllers;

use PP\PaycoinDb;

class Explorer extends Controller {

	public function index() {

		$this->render('header');
		$this->render('index');
		$this->render('footer');

	}

	public function search() {

		$q = $this->bootstrap->httpRequest->get('q');
		$q = trim($q);
		$paycoinDb = new PaycoinDb();
		$results = $paycoinDb->search($q);

		if (count($results) == 1) {
			$result = current($results);
			$url = current(array_values($result));
			header('Location: ' . $url);
			return;
		}

		$this->setData('q', $q);
		$this->setData('results', $results);

		$this->render('header');
		$this->render('search');
		$this->render('footer');
	}

	public function address() {

		$address = $this->bootstrap->route['address'];

		//@todo to do limit we need to get a count...
		$limit = 10000;

		$paycoinDb = new PaycoinDb();

		$addressInformation = $paycoinDb->getAddressInformation($address, $limit);

		$this->setData('address', $address);
		$this->setData('addressInformation', $addressInformation);

		$this->render('header');
		$this->render('address');
		$this->render('footer');
	}

	public function block() {

		$hash = $this->bootstrap->route['hash'];
		$paycoin = new PaycoinDb();
		$block = $paycoin->getBlockByHash($hash);
		if ($block != null) {
			$transactions = $paycoin->getTransactionsInBlock($block['height']);
			foreach ($transactions as $k => $transaction) {
				$transactions[$k]['vout'] = $paycoin->getTransactionsOut($transaction['txid']);
				$transactions[$k]['vin'] = $paycoin->getTransactionsIn($transaction['txid']);
			}
			$this->setData('transactions', $transactions);

		}
		$this->setData('hash', $hash);
		$this->setData('block', $block);


		$this->render('header');
		$this->render('block');
		$this->render('footer');
	}

	public function transaction() {

		$txid = $this->bootstrap->route['txid'];
		$paycoin = new PaycoinDb();

		$transaction = $paycoin->getTransaction($txid);
		$transactionsIn = $paycoin->getTransactionsIn($txid);
		$transactionsOut = $paycoin->getTransactionsOut($txid);

		$this->setData('redeemedIn', $paycoin->getTransactionIn($transaction['txid']));
		$this->setData('transaction', $transaction);
		$this->setData('transactionsIn', $transactionsIn);
		$this->setData('transactionsOut', $transactionsOut);

		$this->render('header');
		$this->render('transaction');
		$this->render('footer');

	}


	public function about() {

		$this->render('header');
		$this->render('about');
		$this->render('footer');
	}

	public function api() {

		$this->render('header');
		$this->render('api');
		$this->render('footer');
	}

	public function contact() {

		$this->render('header');
		$this->render('contact');
		$this->render('footer');
	}

	public function richlist() {

		$paycoin = new PaycoinDb();
		$richList = $paycoin->getRichList();

		$this->setData('richList', $richList);
		$this->render('header');
		$this->render('richlist');
		$this->render('footer');

	}

} 