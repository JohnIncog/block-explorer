<?php

namespace controllers;

use lib\Exceptions\RateLimitException;
use lib\PaycoinDb;
use lib\PaycoinRPC;

class Explorer extends Controller {

	public function __construct($bootstrap) {
		parent::__construct($bootstrap);


	}

	public function index() {

		$this->setData('cacheTime', 60);

		$this->render('header');
		$this->render('index');
		$this->render('footer');

	}

	public function search() {

		$q = $this->bootstrap->httpRequest->get('q');
		$q = trim($q);
		$this->setData('q', $q);

		$paycoinDb = new PaycoinDb();

		try {

			$results = $paycoinDb->search($q);

		} catch (RateLimitException $e) {
			if (DEBUG_BAR) {
				$this->bootstrap->debugbar['exceptions']->addException($e);
			}
			$this->setData('pageTitle', 'Search');
			$this->render('header');
			$this->render('ratelimit_exceeded');
			$this->render('footer');
			return;
		}

		if (count($results) == 1) {
			$result = current($results);
			$url = current(array_values($result));
			header('Location: ' . $url);
			return;
		}


		$this->setData('results', $results);

		$this->setData('pageTitle', 'Search');
		$this->render('header');
		$this->render('search');
		$this->render('footer');
	}

	public function address() {

		$address = $this->bootstrap->route['address'];

		$limit = $this->getLimit(100);
		$this->setData('limit', $limit);

		$paycoinDb = new PaycoinDb();

		$addressInformation = $paycoinDb->getAddressInformation($address, $limit);

		$this->setData('address', $address);
		$this->setData('addressInformation', $addressInformation);
		$this->setData('pageTitle', 'Paycoin Address - ' . $address);
		$this->setData('cacheTime', 60);

		$this->render('header');
		$this->render('address');
		$this->render('footer');
	}

	public function primeStakes() {

		$limit = $this->getLimit(25);

		$paycoinDb = new PaycoinDb();
		$primeStakes = $paycoinDb->primeStakes($limit);
		$this->setData('primeStakes', $primeStakes);

		$this->setData('pageTitle', 'Prime Stakes');
		$this->setData('cacheTime', 60);
		$this->render('header');
		$this->render('primestakes');
		$this->render('footer');
	}

	public function latestTransactions() {

		$limit = $this->getLimit(25);
		$paycoinDb = new PaycoinDb();
		$transactions = $paycoinDb->getLatestTransactions($limit);
		$this->setData('transactions', $transactions);

		$this->setData('pageTitle', 'Latest Transactions');
		$this->setData('cacheTime', 60);
		$this->render('header');
		$this->render('latesttransactions');
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
		$this->setData('pageTitle', 'Paycoin Block - ' . (int)$block['height']);
		$this->setData('cacheTime', 60);

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

		$this->setData('pageTitle', 'Paycoin Transaction - ' . $txid);
		$this->setData('cacheTime', 60);

		$this->render('header');
		$this->render('transaction');
		$this->render('footer');

	}


	public function about() {

		$this->setData('pageTitle', 'About');
		$this->setData('pageName', 'About');
		$this->setData('cacheTime', 3600);

		$this->render('header');
		$this->render('about');
		$this->render('footer');
	}

	public function api() {

		$this->setData('pageTitle', 'API');
		$this->setData('pageName', 'API');
		$this->setData('cacheTime', 3600);

		$this->render('header');
		$this->render('api');
		$this->render('footer');
	}

	public function contact() {


		if ($this->bootstrap->httpRequest->getRealMethod() == 'POST') {
			$siteConfig = $this->getConfig('site');
			$message = $this->bootstrap->httpRequest->get('message');
			$name = $this->bootstrap->httpRequest->get('name');
			$email = $this->bootstrap->httpRequest->get('email');

			$emailBody = "Contact Us Submission From https://ledger.paycoin.com/contact\n";
			$emailBody .= "From: $name <{$email}> \n";
			$emailBody .= "\n{$message}\n";
			$emailBody .= "\nIP Address: {$_SERVER['REMOTE_ADDR']}\n";

			if (mail($siteConfig['contactEmails'], 'Contact', $emailBody)) {
				$this->setData('sent', true);
			} else {
				$this->setData('error', 'Error sending email.  Please email support@paycoin.com');
			}
		}

		$this->setData('pageTitle', 'Contact');
		$this->setData('pageName', 'Contact');

		$this->render('header');
		$this->render('contact');
		$this->render('footer');
	}

	public function richlist() {

		$limit = $this->getLimit(25);
		$paycoin = new PaycoinDb();
		$richList = $paycoin->getRichList($limit);

		$distribution = $paycoin->getRichListDistribution();

		$this->setData('cacheTime', 60);

		$this->setData('distribution', $distribution);
		$this->setData('richList', $richList);
		$this->setData('pageTitle', 'Paycoin Rich List');
		$this->render('header');
		$this->render('richlist');
		$this->render('footer');

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

	public function test() {
		$paycoin = new PaycoinRPC();
		echo '<pre>';
		var_dump($paycoin->getBlockCount());
		var_dump($paycoin->getInfo());
		echo '</pre>';
	}

} 