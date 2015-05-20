<?php

//#todo blcok Difficulty	0.18723002 diff cutoff... check db etc.

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

		$paycoinDb = new PaycoinDb();
		$results = $paycoinDb->search($q);

		//@todo if only one result then redirect.

		$this->setData('q', $q);
		$this->setData('results', $results);

		$this->render('header');
		$this->render('search');
		$this->render('footer');
	}

	public function address() {

		$wallet = $this->bootstrap->route['address'];

		$this->setData('address', $wallet);

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

		$this->setData('transaction', $transaction);
		$this->setData('transactionsIn', $transactionsIn);
		$this->setData('transactionsOut', $transactionsOut);

		if ($this->bootstrap->httpRequest->get('debug')) {
			echo "<pre>";
			var_dump($transaction);
			var_dump($transactionsIn);
			var_dump($transactionsOut);
			echo "</pre>";
		}

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

} 