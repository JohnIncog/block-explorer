<?php

namespace PP;

class PaycoinDb {

	public $mysql;

	public function __construct() {
		$this->mysql = new Mysql();
	}

	public function getBlockByHeight($blockHeight) {
		$blockHeight = (int)$blockHeight;
		$block = $this->mysql->selectRow("SELECT * FROM blocks b WHERE `height` = $blockHeight ");

		return $block;
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

	/**
	 * Used to determine Redeemed in
	 * @param $txid
	 * @return array
	 */
	public function getTransactionIn($txid) {

		$sql = "SELECT  * from transactions_in WHERE `txid` = " . $this->mysql->escape($txid);
		$transactions = $this->mysql->select($sql);

		return $transactions;
	}

	public function getTransaction($txId) {

		$transaction = $this->mysql->selectRow("
			SELECT t.*, b.flags, b.hash FROM transactions t
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
		if ($startBlockHeight > 1) {
			$previousBlock = $this->getBlockByHeight($startBlockHeight-1);
			$outstanding = $previousBlock['outstanding'];
		}

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

	public function getAddressTransactions($address, $limit = 100000) {

		$return['totalInTransactions'] = 0;
		$return['totalStakeTransactions'] = 0;
		$return['totalOutTransactions'] = 0;
		$return['totalInValue'] = 0;
		$return['totalOutValue'] = 0;
		$return['totalStakeValue'] = 0;

		$limit = (int)$limit;
		$sql = "SELECT `txidp`, SUM(`value`) AS `value`, tri.`time`, COUNT(*) as cnt, t.block_height, tri.*
				FROM transactions_in `tri`
				JOIN transactions t ON tri.txidp = t.txid
				WHERE address=" . $this->mysql->escape($address) . "
				GROUP BY txidp
				ORDER BY t.block_height DESC LIMIT $limit ";

		//if count > 2 then its a receive else its a stake.

		echo '<pre>';
		var_dump($sql);
		echo '</pre>';

		$transactions = $this->mysql->select($sql);
		foreach ($transactions as &$transaction) {
			if ($transaction['cnt'] > 2) {
				$transaction['type'] = 'Sent';
//				$return['totalOutTransactions']++;
			//} elseif ($transaction['cnt'] > 2) {
			} else {
				$transaction['type'] = 'Stake';

				if ( //address PS43Jt2x3LXCkou2hZPaKjGwb1TQmAaihg
					$transaction['txidp'] == '4a5b15b24ae3cd94b04db90e891954c70411e7e89893df34f0f83f974f4f5a05'
					|| $transaction['txidp'] == '0c6332632bcbbab460800018ac03f7498e68fbfe5f9f4e112a1ad31d51d0903a'
				) {
					//wtf makes these different...
					echo '<pre>';
					var_dump($transaction);
					echo '</pre>';
					$transaction['type'] = 'Sent';
				} else {
					echo '<pre>';
					var_dump('!!', $transaction);
					echo '</pre>';
				}

//				$return['totalStakeTransactions']++;
			}

			$return['transactions'][$transaction['txidp']] = $transaction;
		}

//		echo '<pre>';
//		var_dump($return);
//		echo '</pre>';


		$sql = "SELECT `txidp`, SUM(`value`) AS `value`, tro.`time`, t.block_height,  t.block_height, tro.type
			FROM transactions_out `tro` JOIN transactions t ON tro.txidp = t.txid
			WHERE address=" . $this->mysql->escape($address) . "
			-- AND TYPE='pubkeyhash'
			GROUP BY txidp
			ORDER BY t.block_height DESC LIMIT $limit ";

//
//		echo '<pre>';
//		var_dump($sql);
//		echo '</pre>';

		$transactions = $this->mysql->select($sql);
		foreach ($transactions as &$transaction) {
			$transaction['type'] = 'Received';
//			$return['totalInTransactions']++;

			if (isset($return['transactions'][$transaction['txidp']])) {
				$return['transactions'][$transaction['txidp']]['value'] = $transaction['value'] - $return['transactions'][$transaction['txidp']]['value'];
				if ($return['transactions'][$transaction['txidp']]['value'] < 0) {

					var_dump($transaction);
					var_dump($return['transactions'][$transaction['txidp']]);
					die;
				}

			} else {
				$return['transactions'][$transaction['txidp']] = $transaction;
			}
		}

		$return['totalInTransactions'] = 0;
		$return['totalStakeTransactions'] = 0;
		$return['totalOutTransactions'] = 0;
		$return['totalInValue'] = 0;
		$return['totalOutValue'] = 0;
		$return['totalStakeValue'] = 0;

		foreach ($return['transactions'] as $i => $t) {
			if ($t['type'] == 'Stake') {
				$return['totalStakeTransactions']++;
				$return['totalStakeValue'] += $t['value'];

			} elseif ($t['type'] == 'Sent') {
				$return['totalOutTransactions']++;
				$return['totalOutValue'] += $t['value'];

			} elseif ($t['type'] == 'Received') {
				$return['totalInTransactions']++;
				$return['totalInValue'] += $t['value'];

			}

		}


//		echo '<pre>';
//		var_dump($return);
//		echo '</pre>';


		return $return;

	}

	public function getAddressInformation($address, $limit = 100) {
		$limit = (int)$limit;
		$sql = "SELECT
				*
			FROM (
				SELECT NULL, `txidp`, SUM(`value`) AS `value`, `time`, 'Received' as `type` FROM transactions_out WHERE address=" . $this->mysql->escape($address) . "
				GROUP BY txidp
				UNION
				SELECT `txid`, `txidp`, `value`, `time`, 'Sent' as `type` FROM transactions_in WHERE address=" . $this->mysql->escape($address) . "

			) AS transactions

			JOIN transactions t ON transactions.txidp = t.txid

			ORDER BY t.block_height DESC limit $limit
			";

		echo '<pre>';
		var_dump($sql);
		echo '</pre>';

		$return['transactions'] = $this->mysql->select($sql);

		$balance = 0;
		$return['transactions'] = array_reverse($return['transactions']);

		foreach ($return['transactions'] as $i => $t) {

			if (!empty($return['transactions'][$i-1]) && $return['transactions'][$i-1]['txid'] == $return['transactions'][$i]['txid']) {

				if ($return['transactions'][$i-1]['txFee'] == 0) {
					$return['transactions'][$i]['type'] = 'Stake';
				}
				$return['transactions'][$i]['value'] = $return['transactions'][$i]['value'] - $return['transactions'][$i-1]['value'];
				$balance += $t['value'];
				$return['transactions'][$i]['balance'] = $balance;
				unset($return['transactions'][$i-1]);

			} elseif ($t['type'] == 'Sent') {
				$balance -= $t['value'];
				$return['transactions'][$i]['balance'] = $balance;
			} elseif ($t['type'] == 'Received') {
				$balance += $t['value'];
				$return['transactions'][$i]['balance'] = $balance;
			}

		}

		$return['totalInTransactions'] = 0;
		$return['totalStakeTransactions'] = 0;
		$return['totalOutTransactions'] = 0;
		$return['totalInValue'] = 0;
		$return['totalOutValue'] = 0;
		$return['totalStakeValue'] = 0;

		foreach ($return['transactions'] as $i => $t) {
			if ($t['type'] == 'Stake') {
				$return['totalStakeTransactions']++;
				$return['totalStakeValue'] += $t['value'];

			} elseif ($t['type'] == 'Sent') {
				$return['totalOutTransactions']++;
				$return['totalOutValue'] += $t['value'];

			} elseif ($t['type'] == 'Received') {
				$return['totalInTransactions']++;
				$return['totalInValue'] += $t['value'];

			}

		}

		$return['transactions'] = array_reverse($return['transactions']);
		$last = current($return['transactions']);

		$return['balance'] = $last['balance'];
		return $return;

	}

} 