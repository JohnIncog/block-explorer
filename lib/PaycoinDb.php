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

		$block = $this->mysql->selectRow("SELECT * FROM blocks b WHERE `hash` = " . $this->mysql->escape($hash));

		return $block;
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

		$transaction = $this->mysql->selectRow("
			SELECT t.*, b.flags FROM transactions t
			 JOIN blocks b on b.height = t.block_height
			WHERE t.txid = " . $this->mysql->escape($txId));

		return $transaction;
	}

	public function processTransactions($block) {
		$transactions = $block['tx'];

		$paycoin = new PaycoinRPC();
		$totalValue = 0;
		$totalValueIn = 0;
		$transactionCount = 0;
		foreach ($transactions as $transactions) {
			$transactionCount++;
			$rawTransaction = $paycoin->getRawTransaction($transactions);
			$decodedTransaction = $paycoin->decodeRawTransaction($rawTransaction);

			$vIn = $this->processVin($decodedTransaction);
			$vOut = $this->processVout($decodedTransaction);
			$this->processTX($decodedTransaction);

			$totalValue = bcadd($vOut['valueTotal'], $totalValue, 8);
			$totalValueIn = bcadd($vIn['valueTotal'], $totalValueIn, 8);

			$txFee = bcsub($totalValue, $totalValueIn, 8);
			$txFee = bcsub($txFee, $block['mint'], 8);

			$transactionInsert[] = array(
				'txid' => $decodedTransaction['txid'],
				'version' => $decodedTransaction['version'],
				'time' => $decodedTransaction['time'],
				'locktime' => $decodedTransaction['locktime'],
				'block_height' => $block['height'],
				'txFee' => $txFee,
				'raw' => serialize($decodedTransaction),
			);


		}
		$this->mysql->insertMultiple('transactions', $transactionInsert);
		$return['totalValue'] = $totalValue;
		$return['totalValueIn'] = $totalValueIn;
		$return['transactionCount'] = $transactionCount;

		return $return;
	}

	public function processVin($transaction) {
		$valueTotal  = 0;
		$vins = $transaction['vin'];
		foreach ($vins as $vin) {
			$insert = array();
			$insert['txidp'] = $transaction['txid'];
			$insert['time'] = $transaction['time'];

			if (isset($vin['txid'])) {
				$insert['address'] = $this->getVout($vin['txid'], $vin['vout'], "addresses");
				$insert['value'] =  $this->getVout($vin['txid'], $vin['vout'], "value");
				$valueTotal = bcadd($insert['value'], $valueTotal, 8);
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
		$return['valueTotal'] = $valueTotal;

		return $return;
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
					$valueTotal = bcadd($value, $valueTotal, 8);
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
		$outstanding = 0;
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
			$transactionsReturn = $this->processTransactions($block);


			$blockInsert['transactions'] = $transactionsReturn['transactionCount'];
			$blockInsert['valueout'] = $transactionsReturn['totalValue'];
			$blockInsert['valuein'] = $transactionsReturn['totalValueIn'];


			$outstanding = bcadd($block['mint'], $outstanding, 8);

			if (count($block['tx']) > 1) {
				$txFees = bcsub($blockInsert['valueout'], bcadd($blockInsert['valuein'], $blockInsert['mint'], 8), 8);
				$outstanding = bcadd($outstanding, $txFees, 8);
				$blockInsert['txFees'] = $txFees;
			}

//			if ($block['height'] == 126) {  //first txfee
//			if ($block['height'] == 309) {  //first destroy -0.005
//			if ($block['height'] == 279) {  //first block with 0 coins / fees / stakes (nonstandard)


			$blockInsert['outstanding'] = $outstanding;


			$this->mysql->insert('blocks', $blockInsert);
			$this->mysql->completeTransaction();

		}

		return true;
	}

	public function search($q) {

		$maxResults = 10;
		$maxPerItemResults = 5;
		$return = array();

		//check if address
		if (strlen($q) == 34) {
			$return['Address'][] = '/address/' . urlencode($q);
		}
		//@todo sql for address.

		//check if block height

		if (is_numeric($q) && substr($q, 0, 1) != 0) {

			$block = $this->mysql->selectRow("SELECT `hash`, `height` FROM blocks WHERE `height` = "
				. $this->mysql->escape($q));
			$return['Block Height']['Block ' . $block['height']] = '/block/' . urlencode($block['hash']);


		}

		//check if block hash
		if (count($return) <= $maxResults) {
			$limit = $maxPerItemResults - count($return);
			$blocks = $this->mysql->select("SELECT `hash`, `height` FROM blocks WHERE `hash` LIKE "
				. $this->mysql->escape($q . '%') . ' LIMIT ' . $limit);
			foreach ($blocks as $block) {
				$return['Block']['Block ' . $block['height']] = '/block/' . urlencode($block['hash']);
			}
		}

		//check if transaction

		if (count($return) <= $maxResults) {
			if (count($return) <= $maxResults) {
				$limit = $maxPerItemResults - count($return);
				$transactions = $this->mysql->select("SELECT `txid` FROM transactions WHERE `txid` LIKE "
					. $this->mysql->escape($q . '%') . ' LIMIT ' . $limit);
			}
			foreach ($transactions as $transaction) {
				$return['Transaction'][] = '/transaction/' . urlencode($transaction['txid']);
			}
		}

		return $return;


	}

} 