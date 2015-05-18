<?php

namespace PP;

class PaycoinDb {

	public $mysql;

	public function __construct() {
		$this->mysql = new Mysql();
	}

	public function getBlockByHeight($blockHeight) {
		$blockHeight = (int)$blockHeight;
		$block = $this->mysql->select("SELECT * FROM blocks b WHERE `height` = $blockHeight ");

		return $block[0];
	}

	public function getLatestBlocks($limit) {

		$blocks = $this->mysql->select("SELECT * FROM blocks b ORDER by `height` DESC LIMIT " . (int)$limit);

		return $blocks;
	}

	public function getBlockByHash($hash) {

		$block = $this->mysql->select("SELECT * FROM blocks b WHERE `hash` = " . $this->mysql->escape($hash));

		return $block[0];
	}

	public function getBlockDetails($block) {

		$block['transactions'] = $this->getTransactionsInBlock($block['height']);
		$block['transactionsOut'] = $this->getTransactionsOut($block['height']);
		foreach ($block['transactionsOut'] as $k => $v) {
			$block['transactionsOut'][$k]['raw'] = unserialize($v['raw']);
		}
		$block['raw'] = unserialize($block['raw']);

		return $block;
	}

	public function getTransactionsInBlock($blockHeight) {

		$sql = "SELECT * FROM transactions t WHERE `block_height` = " . $this->mysql->escape($blockHeight);
		$blocks = $this->mysql->select($sql);
		foreach ($blocks as $k => $v) {
			$blocks[$k]['raw'] = unserialize($v['raw']);
		}

		return $blocks;
	}

	public function getTransactionsOut($txid) {

		$sql = "SELECT  * from transactions_out WHERE `txidp` = " . $this->mysql->escape($txid);
		$transactions = $this->mysql->select($sql);

		return $transactions;
	}

	public function getTransactionsIn($txid) {

		$sql = "SELECT  * from transactions_in WHERE `txidp` = " . $this->mysql->escape($txid);
		$transactions = $this->mysql->select($sql);

		return $transactions;
	}

	public function getTransaction($txId) {

		$block = $this->mysql->select("SELECT * FROM transactions t WHERE `txid` = " . $this->mysql->escape($txId));

		return $block[0];
	}

	public function processTransactions($transactions, $height) {

		$paycoin = new PaycoinRPC();
		$totalValue = 0;
		$transactionCount = 0;
		foreach ($transactions as $transactions) {
			$transactionCount++;
			$rawTransaction = $paycoin->getRawTransaction($transactions);
			$decodedTransaction = $paycoin->decodeRawTransaction($rawTransaction);

			$transactionInsert[] = array(
				'txid' => $decodedTransaction['txid'],
				'version' => $decodedTransaction['version'],
				'time' => $decodedTransaction['time'],
				'locktime' => $decodedTransaction['locktime'],
				'block_height' => $height,
				'raw' => serialize($decodedTransaction),
			);

			$this->processVin($decodedTransaction);
			$vOut = $this->processVout($decodedTransaction);
			$this->processTX($decodedTransaction);
			$totalValue = bcadd($vOut['valueTotal'], $totalValue, 6);
		}
		$this->mysql->insertMultiple('transactions', $transactionInsert);
		$return['totalValue'] = $totalValue;
		$return['transactionCount'] = $transactionCount;

		return $return;
	}

	public function processVin($transaction) {

		$vins = $transaction['vin'];
		foreach ($vins as $vin) {
			$insert = array();
			$insert['txidp'] = $transaction['txid'];
			$insert['time'] = $transaction['time'];

			if (isset($vin['txid'])) {
				$insert['address'] = $this->getVout($vin['txid'], $vin['vout'], "addresses");
				$insert['value'] = $b = $this->getVout($vin['txid'], $vin['vout'], "value");
			}
			foreach ($vin as $key => $value) {
				if ($key == 'scriptSig') {
					foreach ($value as $ke => $val) {
						$insert[$ke] = $val;
					}
				} else {
					$insert[$key] = $value;
				}
			}
			$this->mysql->insert('transactions_in', $insert);
		}
		//$this->mysql->insertMultiple('transactions_in', $insert);
	}

	public function getVout($txid, $vout, $type) {

		$paycoin = new PaycoinRPC();
		$rawTransaction = $paycoin->getRawTransaction($txid);
		$transaction = $paycoin->decodeRawTransaction($rawTransaction);
		if ($type == "addresses") {
			$return = $transaction['vout'][$vout]["scriptPubKey"]["addresses"][0];
		} else {
			$return = $transaction['vout'][$vout][$type];
		}

		return $return;
	}

	public function processVout($transaction) {

		$valueTotal  = 0;
		$vouts = $transaction['vout'];
		foreach ($vouts as $vout) {
			$insert = array();
			$insert['txidp'] = $transaction['txid'];
			$insert['time'] = $transaction['time'];
			foreach ($vout as $key => $value) {
				if ($key == "value") {
					$valueTotal = bcadd($value, $valueTotal, 6);
				}
				if ($key == "scriptPubKey") {
					foreach ($value as $ke => $val) {
						if ($ke == "addresses") {
							$insert['address'] = $val[0];
						} else {
							$insert[$ke] = $val;
						}
					}
				} else {
					$insert[$key] = $value;
				}
			}
			$this->mysql->insert('transactions_out', $insert);

		}
		$return['valueTotal'] = $valueTotal;

		return $return;
	}

	public function processTX($tx) {

		$insert = array();
		foreach ($tx as $key => $value) {
			if ($key == "vin") {
			} elseif ($key == "vout") {
			} else {
				$insert[$key] = $value;
			}
		}

		return "Completed";
	}

	public function getLastBlockInDb() {

		$r = $this->mysql->selectRow("SELECT MAX(`height`) as `height` FROM `blocks`");
		if ($r['height'] == NULL) {
			$return = 0;
		} else {
			$return = $r['height'];
		}

		return $return;
	}

	public function buildDb($startBlockHeight, $endBlockHeight) {

		$paycoinRPC = new PaycoinRPC();
		for ($i = $startBlockHeight; $i < $endBlockHeight; $i++) {
			$blockHash = $paycoinRPC->getBlockHash($i);
			$block = $paycoinRPC->getBlock($blockHash);
			//echo "Block Height {$block['height']}" . PHP_EOL;
			$blockInsert = array(
				'hash' => $block['hash'],
				'size' => $block['size'],
				'height' => $block['height'],
				'version' => $block['version'],
				'merkleroot' => $block['merkleroot'],
				'time' => $block['time'],
				'nonce' => $block['nonce'],
				'bits' => $block['bits'],
				'difficulty' => $block['difficulty'],
				'mint' => $block['mint'],
				'previousblockhash' => Helper::getValue($block, 'previousblockhash'),
				'nextblockhash' => $block['nextblockhash'],
				'flags' => $block['flags'],
				'proofhash' => $block['proofhash'],
				'entropybit' => $block['entropybit'],
				'modifier' => $block['modifier'],
				'modifierchecksum' => $block['modifierchecksum'],
				'raw' => serialize($block),
			);

			$this->mysql->startTransaction();
			$transactionsReturn = $this->processTransactions($block['tx'], $block['height']);
			$blockInsert['transactions'] = $transactionsReturn['transactionCount'];
			$blockInsert['valueout'] = $transactionsReturn['totalValue'];
			$this->mysql->insert('blocks', $blockInsert);
			$this->mysql->completeTransaction();

		}

		return true;
	}

} 