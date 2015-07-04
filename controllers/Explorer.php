<?php
/**
 * @author John <john@paycoin.com>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */
namespace controllers;

use lib\Exceptions\RateLimitException;
use lib\PaycoinDb;
use lib\PaycoinRPC;
use lib\User;

/**
 * Class Explorer
 * @package controllers
 */
class Explorer extends Controller {

	public function __construct($bootstrap) {
		parent::__construct($bootstrap);


	}

	public function index() {

		$this->setData('activeTab', 'Blocks');

		$siteConfig = $this->getConfig('site');
		$this->setData('pageTitle', $siteConfig['name'] . ' - Paycoin Block Explorer');

		$this->addJs('/js/timeago.min.js');
		$this->addJs('/js/index.js');
		$this->addJs('/js/market_info.js');
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
			if (count($result) == 1) {
				$url = current(array_values($result));
				header('Location: ' . $url);
				return;
			}
		}


		$this->setData('results', $results);

		$this->setData('pageTitle', 'Search');
		$this->render('header');
		$this->render('search');
		$this->render('footer');
	}

	public function address() {


		$this->addJs('/js/address.js');
		$this->addJs('/js/jquery.qrcode-0.12.0.min.js');
		$this->addJs('/js/jquery.qrcode-0.12.0.min.js');
		$this->addJs('/js/stupidtable.min.js');

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

		$this->setData('activeTab', 'Prime Stakes');
		$this->setData('enableLimitSelector', true);

		$this->addJs('/js/market_info.js');
		$this->addJs('/js/update_outstanding.js');

		$limit = $this->getLimit(25);

		$paycoinDb = new PaycoinDb();
		$primeStakes = $paycoinDb->primeStakes($limit);
		$addresses = array();
		foreach ($primeStakes as $primeStake) {
			$addresses[] = $primeStake['address'];
		}
		$this->setData('addressTagMap', $paycoinDb->getAddressTagMap($addresses));
		$this->setData('primeStakes', $primeStakes);

		$this->setData('pageTitle', 'Prime Stakes');
		$this->setData('cacheTime', 60);
		$this->render('header');
		$this->render('primestakes');
		$this->render('footer');
	}

	public function network() {

		$this->setData('activeTab', 'Network');
		$this->setData('activePulldown', 'Versions');
		$this->setData('enableLimitSelector', true);


		$this->addJs('/js/network.js');
		$this->addJs('/js/market_info.js');
		$this->addJs('/js/update_outstanding.js');

		$this->addJs('/highcharts/js/highcharts.js');

		$this->addJs('/js/charts/theme.js');

		$this->addJs('/highcharts/js/highcharts-3d.js');
		$this->addJs('/highcharts/js/modules/exporting.js');



		$limit = $this->getLimit(25);

		$paycoin = new PaycoinDb();
		$network = $paycoin->getNetwork();

		$this->setData('network', $network);
		$this->setData('pageTitle', 'Network');
		$this->setData('cacheTime', 60);

		$this->render('header');
		$this->render('network');
		$this->render('footer');
	}

	public function networkMap() {

		$this->setData('activeTab', 'Network');
		$this->setData('activePulldown', 'Network Map');

		$this->setData('enableLimitSelector', true);


		$this->addJs('/js/network_map.js');
		$this->addJs('/js/market_info.js');
		$this->addJs('/js/update_outstanding.js');

		$this->addJs('/highmaps/js/highmaps.js');
		$this->addJs('/highmaps/js/modules/exporting.js');

		$this->addJs('//code.highcharts.com/mapdata/custom/world-highres.js');

		$paycoin = new PaycoinDb();
		$networkData = $paycoin->getNetworkMapData();
		$limit = $this->getLimit(25);
		$network = $paycoin->getNetworkByCity($limit);

		$this->setData('network', $network);
		$this->setData('networkData', $networkData);
		$this->setData('pageTitle', 'Network Map');
		$this->setData('cacheTime', 60);

		$this->render('header');
		$this->render('network_map');
		$this->render('footer');
	}


	public function latestTransactions() {

		$this->setData('activeTab', 'Transactions');
		$this->setData('enableLimitSelector', true);

		$this->addJs('/js/market_info.js');
		$this->addJs('/js/update_outstanding.js');
		$this->addJs('/js/latesttransactions.js');
		$this->addJs('/js/timeago.min.js');

		$limit = $this->getLimit(25);
		$paycoinDb = new PaycoinDb();
		$transactions = $paycoinDb->getLatestAddressTransactions($limit);

		$addresses = array();
		foreach ($transactions as $transaction) {
			$addresses[] = $transaction['address'];
		}
		$this->setData('addressTagMap', $paycoinDb->getAddressTagMap($addresses));

		$this->setData('transactions', $transactions);

		$this->setData('pageTitle', 'Latest Transactions');

		$this->setData('cacheTime', 60);
		$this->render('header');
		$this->render('latesttransactions');
		$this->render('footer');
	}

	public function block() {

		$this->addJs('/js/block.js');

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

		$this->addJs('/js/transaction.js');
		$txid = $this->bootstrap->route['txid'];
		$paycoin = new PaycoinDb();

		$this->setBlockHeight();

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

		$this->setData('activeTab', 'About');

		$this->addJs('/js/market_info.js');
		$this->addJs('/js/update_outstanding.js');


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

		$this->setData('activeTab', 'Rich List');
		$this->setData('enableLimitSelector', true);

		$this->addJs('/js/market_info.js');
		$this->addJs('/js/update_outstanding.js');
		$this->addJs('/js/richlist.js');
		$this->addJs('/js/charts/theme.js');
		$this->addJs('/highcharts/js/highcharts.js');
		$this->addJs('/highcharts/js/highcharts-3d.js');
		$this->addJs('/highcharts/js/modules/exporting.js');

		$limit = $this->getLimit(25);
		$paycoin = new PaycoinDb();
		$richList = $paycoin->getRichList($limit);

		$addresses = array();
		$addressTagMap = array();
		foreach ($richList as $rich) {
			$addresses[] = $rich['address'];
		}
		if (count($addresses) > 0) {
			$addressTagMap = $paycoin->getAddressTagMap($addresses);
		}
		$this->setData('addressTagMap', $addressTagMap);

		$distribution = $paycoin->getRichListDistribution();

		$this->setData('cacheTime', 60);

		$this->setData('distribution', $distribution);
		$this->setData('richList', $richList);
		$this->setData('pageTitle', 'Paycoin Rich List');
		$this->render('header');
		$this->render('richlist');
		$this->render('footer');

	}

	public function primeBids() {

		$limit = $this->getLimit(25);
		$paycoin = new PaycoinDb();

		$startDate = '2015-07-01';
		$this->setData('startDate', strtotime($startDate));

		$roundDays = 7;


		$diff = date_diff(new \DateTime($startDate), new \DateTime('now'));
		$currentRound = 'Starts in ' . ceil($diff->days) . ' Days';
		$primeBids = array();
		if (time() > strtotime($startDate)) {
			$currentRound = ceil($diff->days / $roundDays) + 1 . ' of 25';
			$primeBids = $paycoin->getPrimeBids($limit);
		}

		$this->setData('currentRound', $currentRound);

		$this->setData('activeTab', 'Prime Bids');
		$this->setData('enableLimitSelector', true);

		$this->addJs('/js/market_info.js');
		$this->addJs('/js/update_outstanding.js');


		$addresses = array();
		$addressTagMap = array();
		if (count($primeBids) > 0) {
			foreach ($primeBids as $primeBid) {
				$addresses[] = $primeBid['address'];
			}
		}
		if (count($addresses) > 0) {
			$addressTagMap = $paycoin->getAddressTagMap($addresses);
		}
		$this->setData('addressTagMap', $addressTagMap);


		$this->setData('cacheTime', 60);

		$this->setData('primeBids', $primeBids);
		$this->setData('primeBidders', $paycoin->getPossibleBidders());
		$this->setData('pageTitle', 'Paycoin Prime Controller Bids');
		$this->render('header');
		$this->render('primebids');
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
		$this->setData('limit', $limit);
		return $limit;
	}

	private function setBlockHeight() {
		$paycoin = new PaycoinDb();
		$blockHeight = $paycoin->getLastBlockInDb();
		$this->setData('blockHeight', $blockHeight);
	}

	public function tagging() {
		$this->addJs('/js/tagging.js');

		$message = 'Paycoin Blockchain';
		$this->setData('messageToSign', $message);

		$this->setData('pageTitle', 'Tag a Paycoin address');
		$this->setData('success', false);
		if ($this->bootstrap->httpRequest->getRealMethod() == 'POST') {

			$address = $this->bootstrap->httpRequest->request->getAlnum('address');
			$tag = $this->bootstrap->httpRequest->get('tag');
			$signature = $this->bootstrap->httpRequest->request->get('signature');
			$url = $this->bootstrap->httpRequest->request->get('url');

			$message = 'Paycoin Blockchain';
			$this->setData('messageToSign', $message);
			$this->setData('address', $address);
			$this->setData('tag', $tag);
			$this->setData('url', $url);

			$paycoinRpc = new PaycoinRPC;
			$error = false;
			if (!empty($url)) {
				$pu = parse_url($url);
				if (empty($pu['scheme']) || empty($pu['host'])) {
					$error = 'Invalid URL';
				}
			}
			if (empty($address)) {
				$error = 'Invalid Address';
			}
			if (empty($signature)) {
				$error = 'Invalid Signature';
			}
			if (empty($tag)) {
				$error = 'Invalid Tag';
			}


			if (empty($error)) {

				$isVerified = $paycoinRpc->verifySignedMessage($address, $signature, $message);
				if ($isVerified === true) {

					$this->setData('success', true);
					$paycoinDb = new PaycoinDb();
					$paycoinDb->addTagToAddress($address, $tag, $url, 1);

				} elseif ($isVerified === false) {
					$this->setData('error', 'Failed to Verify Message');
				} elseif ($isVerified !== true) {
					$this->setData('error', $isVerified);
				} else {
					$this->setData('error', 'Unknown error');
				}
			} else {
				$this->setData('error', $error);
			}


		}

		$this->setData('pageName', 'Address Tagging');

		$this->render('header');
		$this->render('tagging');
		$this->render('footer');

	}

	public function faq() {

		$siteConfig = $this->getConfig('site');
		$this->setData('pageTitle', 'FAQ - ' . $siteConfig['name']);

		$this->setData('pageName', 'FAQ');

		$this->render('header');
		$this->render('faq');
		$this->render('footer');


	}



	public function test() {
		$user = new User();
		//var_dump($user->addUser('john', 'john@paycoin.com', 'test'));
		var_dump($user->login('john', 'test'));
	}
} 