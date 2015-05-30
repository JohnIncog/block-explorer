<?php
/**
 *
 * Features/Ideas
 *
 * Address Tagging
 * Allow user to tag an address with a name.  This name will show along side address and allow for a url.
 * If an address is tagged by multiple users, tag will be removed and a signed message will be required for a tag to be reapplied.
 *
 * Address Monitor
 * Flag an address to be monitored and receive alerts when it makes transactions
 * Alert types: in browser, email, sms, web hook
 *
 *
 *
 *
 *
 * Live updates
 * done - Latest blocks
 * Latest transaction
 *
 *
 * Graphs
 * Inflation
 * Rich list
 * Difficulty
 * Value out
 * Number of transactions
 * Time between blocks?
 *
 *
 *
 */
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

		$this->setData('pageTitle', 'Search');
		$this->render('header');
		$this->render('search');
		$this->render('footer');
	}

	public function address() {

		$address = $this->bootstrap->route['address'];

		$limit = $this->bootstrap->httpRequest->get('limit');
		if (!$limit) {
			$limit = 100;
		}
		$this->setData('limit', $limit);

		$paycoinDb = new PaycoinDb();

		$addressInformation = $paycoinDb->getAddressInformation($address, $limit);

		$this->setData('address', $address);
		$this->setData('addressInformation', $addressInformation);
		$this->setData('pageTitle', 'Paycoin Address - ' . $address);

		$this->render('header');
		$this->render('address');
		$this->render('footer');
	}

	public function primeStakes() {

		$limit = 100;

		$paycoinDb = new PaycoinDb();
		$primeStakes = $paycoinDb->primeStakes($limit);
		$this->setData('primeStakes', $primeStakes);

		$this->setData('pageTitle', 'Prime Stakes');
		$this->render('header');
		$this->render('primestakes');
		$this->render('footer');
	}

	public function latestTransactions() {

		$limit = 100;

		$paycoinDb = new PaycoinDb();
		$transactions = $paycoinDb->getLatestTransactions($limit);
		$this->setData('transactions', $transactions);

		$this->setData('pageTitle', 'Latest Transactions');
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
		$this->render('header');
		$this->render('transaction');
		$this->render('footer');

	}


	public function about() {

		$this->setData('pageTitle', 'About');
		$this->setData('pageName', 'About');

		$this->render('header');
		$this->render('about');
		$this->render('footer');
	}

	public function api() {

		$this->setData('pageTitle', 'API');
		$this->setData('pageName', 'API');

		$this->render('header');
		$this->render('api');
		$this->render('footer');
	}

	public function contact() {

		if ($this->bootstrap->httpRequest->getRealMethod() == 'POST') {

			//$this->setData('sent', true);
			//@todo get support address
			$this->setData('error', 'Error sending email.  Please email XXXXXXXXX');
		}

		$this->setData('pageTitle', 'Contact');
		$this->setData('pageName', 'Contact');

		$this->render('header');
		$this->render('contact');
		$this->render('footer');
	}

	public function richlist() {

		$paycoin = new PaycoinDb();
		$richList = $paycoin->getRichList();

		$this->setData('richList', $richList);
		$this->setData('pageTitle', 'Paycoin Rich List');
		$this->render('header');
		$this->render('richlist');
		$this->render('footer');

	}

} 