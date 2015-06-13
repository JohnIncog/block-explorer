<?php
/**
 * @author John <john@paycoin.com>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */
namespace controllers;
use lib\PaycoinDb;

/**
 * Class Api
 * @package controllers
 */
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
		$this->outputJsonResponse($block);
	}



	public function getBlockByHash() {
		$hash = $this->bootstrap->route['hash'];
		$paycoin = new PaycoinDb();

		$block = $paycoin->getBlockByHash($hash);
		$block['transaction'] = $paycoin->getTransactionsInBlock($block['height']);

		$this->outputJsonResponse($block);
	}



	public function getTransaction() {
		$txid = $this->bootstrap->route['txid'];
		$paycoin = new PaycoinDb();
		$transaction = $paycoin->getTransaction($txid);
		$transaction['raw'] = unserialize($transaction['raw']);

		$this->outputJsonResponse($transaction);

	}


	public function getLatestBlocks() {

		$height = $this->bootstrap->httpRequest->get('height');
		$limit = $this->getLimit(10, 100);

		$paycoin = new PaycoinDb();
		$blocks = $paycoin->getLatestBlocks($limit, $height);
		foreach ($blocks as &$block) {
			$block['raw'] = unserialize($block['raw']);
		}
		$this->outputJsonResponse($blocks);
	}

	public function getLatestTransactions() {

		$limit = $this->getLimit();

		$paycoinDb = new PaycoinDb();
		$transactions = $paycoinDb->getLatestTransactions($limit);
		$this->outputJsonResponse($transactions);
	}


	public function getAddress() {

		$address = $this->bootstrap->route['address'];

		$limit = $this->getLimit();

		$paycoinDb = new PaycoinDb();

		$addressInformation = $paycoinDb->getAddressInformation($address, $limit);
		$this->outputJsonResponse($addressInformation);
	}

	public function getRichlist() {

		$paycoin = new PaycoinDb();
		$richList = $paycoin->getRichList();
		$this->outputJsonResponse($richList);

	}

	public function getPrimeStakes() {

		$limit = $this->getLimit();

		$paycoinDb = new PaycoinDb();
		$primeStakes = $paycoinDb->primeStakes($limit);
		$this->outputJsonResponse($primeStakes);
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

	public function outputJsonResponse($data) {

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

	public function disputeAddressTag() {

		$address = $this->bootstrap->httpRequest->request->getAlnum('address');
		$paycoinDb = new PaycoinDb();
		$paycoinDb->disputeAddressTag($address);

		$response = array(
			'success' => true,
			'message' => 'Tag has been removed and tagging disabled. <a href="#" class="a-normal">Claim Address</a> to add a Tag .'
		);
		$this->outputJsonResponse($response);
	}

	public function tagAddress() {

		$address = $this->bootstrap->httpRequest->request->getAlnum('address');
		$tag = $this->bootstrap->httpRequest->request->getAlnum('tag');

		if (empty($address)) {
			$response = array(
				'success' => false,
				'error' => 'Error no address in request.'
			);
		} elseif (empty($tag)) {
			$response = array(
				'success' => false,
				'error' => 'You need to enter a tag for the address.'
			);
		} else {

			$paycoinDb = new PaycoinDb();

			try {
				$paycoinDb->addTagToAddress($address, $tag);
			} catch (\Exception $e) {
				if (stristr($e->getMessage(), 'Duplicate') !== false) {
					$response = array(
						'success' => false,
						'error' => 'This address is already tagged. Tagging disabled. <a href="#" class="a-normal">Claim Address</a> to add a Tag',
					);
					$this->disputeAddressTag($address);
				} else {
					$response = array(
						'success' => false,
//						'error' => 'Error adding tag to address.',
						'error' => $e->getMessage()
					);
				}
			}
			if (empty($response)) {
				$response = array(
					'address' => $address,
					'tag' => $tag,
					'success' => true,
					'error' => false
				);
			}

		}

		$this->outputJsonResponse($response);
	}

} 