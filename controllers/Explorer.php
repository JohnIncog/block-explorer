<?php

namespace controllers;

use PP\PaycoinDb;

class Explorer extends Controller {

	public function index() {

		$this->render('header');
		$this->render('index');
		$this->render('footer');

	}

	public function block() {

		$hash = $this->bootstrap->route['hash'];
		$paycoin = new PaycoinDb();
		$block = $paycoin->getBlockByHash($hash);

		$transactions = $paycoin->getTransactionsInBlock($block['height']);
		foreach ($transactions as $k => $transaction) {
			$transactions[$k]['vout'] = $paycoin->getTransactionsOut($transaction['txid']);
			$transactions[$k]['vin'] = $paycoin->getTransactionsIn($transaction['txid']);
		}

		$this->setData('hash', $hash);
		$this->setData('block', $block);
		$this->setData('transactions', $transactions);

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
		$this->setData('transactionIns', $transactionsIn);
		$this->setData('transactionOuts', $transactionsOut);

		$this->render('header');
		$this->render('transaction');
		$this->render('footer');

	}
} 