<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 5/15/15
 * Time: 10:17 PM
 */

namespace controllers;
use PP\PaycoinDb;

class Api extends Controller {

	public function __construct($bootstrap) {
		parent::__construct($bootstrap);
		Header('Content-Type: application/json');



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
		echo json_encode($block);
	}



	public function getBlockByHash() {
		$hash = $this->bootstrap->route['hash'];
		$paycoin = new PaycoinDb();

		$block = $paycoin->getBlockByHash($hash);
		$block['transaction'] = $paycoin->getTransactionsInBlock($block['height']);

		echo json_encode($block);
	}



	public function getTransaction() {
		$txid = $this->bootstrap->route['txid'];
		$paycoin = new PaycoinDb();
		$transaction = $paycoin->getTransaction($txid);
		$transaction['raw'] = unserialize($transaction['raw']);

		echo json_encode($transaction);

	}


	public function getLatestBlocks() {

		$paycoin = new PaycoinDb();
		$blocks = $paycoin->getLatestBlocks(10);
		foreach ($blocks as &$block) {
			$block['raw'] = unserialize($block['raw']);
		}
		echo json_encode($blocks);
	}

	public function test() {
		echo "<pre>\n";
		$paycoin = new Paycoin();
		print_r($paycoin->getLastBlocks());
		print_r($paycoin->getInfo());
		print_r($paycoin->help());
		return;
		echo "</pre>";
	}

} 