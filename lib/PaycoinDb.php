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

	public function getLatestBlocks($limit, $height = 0) {

		$sql = "SELECT * FROM blocks b ";
		$sortOrder = 'DESC';
		if ($height > 0) {
			$sql .= " WHERE `height` >= " .(int)$height;
			$sortOrder = 'ASC';
		}
		$sql .= " ORDER by `height` {$sortOrder} LIMIT " . (int)$limit;
		$blocks = $this->mysql->select($sql);

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
		foreach ($transactions as $transaction) {
			$transactionCount++;
			$rawTransaction = $paycoin->getRawTransaction($transaction);
			$decodedTransaction = $paycoin->decodeRawTransaction($rawTransaction);

			$vIn = $this->processVin($decodedTransaction);
			$vOut = $this->processVout($decodedTransaction);
			$this->processTX($decodedTransaction);

			$totalValue = bcadd($vOut['valueTotal'], $totalValue, 8);
			$totalValueIn = bcadd($vIn['valueTotal'], $totalValueIn, 8);

			$txFee = bcsub($totalValue, $totalValueIn, 8);
			$txFee = bcsub($txFee, $block['mint'], 8);

			$transactionInsert = array(
				'txid' => $decodedTransaction['txid'],
				'version' => $decodedTransaction['version'],
				'time' => $decodedTransaction['time'],
				'locktime' => $decodedTransaction['locktime'],
				'block_height' => $block['height'],
				'txFee' => $txFee,
				'raw' => serialize($decodedTransaction),
			);
			$this->mysql->insert('transactions', $transactionInsert , true);
			$this->addTransactionToAddress($transactionInsert);
		}
		//$this->mysql->insertMultiple('transactions', $transactionInsert);
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
			$this->mysql->insert('transactions_in', $insert, true);
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
			$this->mysql->insert('transactions_out', $insert, true);

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

		for ($i = $startBlockHeight; $i <= $endBlockHeight; $i++) {
			$blockHash = $paycoinRPC->getBlockHash($i);
			$block = $paycoinRPC->getBlock($blockHash);


			$sql = 'UPDATE blocks SET nextblockhash = ' . $this->mysql->escape($block['hash']) .  ' WHERE `height` =' . ($block['height']-1);
			$this->mysql->query($sql);

			echo "Block Height {$block['height']}" . PHP_EOL;
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
				'nextblockhash' => Helper::getValue($block, 'nextblockhash'),
				'flags' => $block['flags'],
				'proofhash' => $block['proofhash'],
				'entropybit' => $block['entropybit'],
				'modifier' => $block['modifier'],
				'modifierchecksum' => $block['modifierchecksum'],
				'raw' => serialize($block),
				'timestamp' => strtotime($block['time'])
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

	public function getAddressInformation($address, $limit = 100000) {

		$sql = "SELECT w.*, rl.rank FROM wallets w "
			.  " LEFT JOIN richlist rl on rl.address= w.address"
			. " WHERE w.address = " . $this->mysql->escape($address)
			.  " ORDER BY w.id DESC";
		if ($limit > 0 && $limit != 'all') {
			$sql .= " LIMIT " . (int)$limit;
		}

		$transactions = $this->mysql->select($sql);

		$return['transactions'] = $transactions;
		$return['address'] = $address;

		$last = current($transactions);
		$return['rank'] = $last['rank'];
		$return['balance'] = $last['balance'];


		$sql = "SELECT `address`, `type`, SUM(`value`) as `sum`, COUNT(*) as `transactions` FROM wallets WHERE address = " . $this->mysql->escape($address)
			.  "GROUP BY `address`, `type` ";

		$totals = $this->mysql->select($sql);
		$return['totalTransactions'] = 0;

		foreach ($totals as $total) {
			if ($total['type'] == 'receive' ) {
				$return['totalInValue'] = $total['sum'];
				$return['totalInTransactions'] = $total['transactions'];
			} if ($total['type'] == 'send') {
				$return['totalOutValue'] = str_replace('-', '',$total['sum']);
				$return['totalOutTransactions'] = $total['transactions'];
			} elseif ($total['type'] == 'stake') {
				$return['totalStakeValue'] = $total['sum'];
				$return['totalStakeTransactions'] = $total['transactions'];
			}
			$return['totalTransactions'] += $total['transactions'];

		}
		return $return;

	}

	public function buildWalletDb() {

		$limit = 1000;

		$q = $this->mysql->selectRow('SELECT id FROM transactions WHERE txid = (SELECT txid FROM wallets ORDER BY id DESC LIMIT 1)');
		$offset = (int)$q['id'];


		$q = $this->mysql->selectRow('SELECT MAX(`id`) as `max` from transactions');
		$last = (int)$q['max'];

		for ($i = $offset; $i <= $last; $i = $i+$limit) {
//			echo "$i, $limit" . PHP_EOL;

			$sql = "SELECT * FROM transactions ORDER BY id LIMIT {$i}, {$limit}";
			$transactions = $this->mysql->select($sql);

			foreach ($transactions as $transaction) {

				$this->addTransactionToAddress($transaction);

			}
		}

	}

	/**
	 *
	 * Add/Update address balance tracking.
	 *
	 * @param array $transaction req keys = txid, block_height, time
	 *
	 */
	public function addTransactionToAddress(array $transaction) {

		//$this->mysql->startTransaction();

		$sql = "SELECT * FROM transactions_in WHERE value IS NOT NULL AND txidp = " . $this->mysql->escape($transaction['txid']);
		$transactionsIn = $this->mysql->select($sql);

		$sql = "SELECT * FROM transactions_out WHERE txidp = " . $this->mysql->escape($transaction['txid']);
		$transactionsOut = $this->mysql->select($sql);

		if (count($transactionsIn) == 0) { //only outputs.. creation?
			$a = array();
			$value = 0;

			foreach ($transactionsOut as $transactionOut) {
				if (empty($transactionOut['address'])) {
					continue;
				}
				if (!isset($a[$transactionOut['address']])) {
					$a[$transactionOut['address']] = 0;
				}
				if ($transactionOut['value'] > 0) {
					$value += $transactionOut['value'];
					$a[$transactionOut['address']] = $value;
				}

			}

			if (count($a) > 0) {
				//echo "creation {$transaction['txid']}" . PHP_EOL;
				foreach ($a as $address => $value) {
					$insert = array(
						'address' => $address,
						'value' => $value,
						'txid' => $transaction['txid'],
						'block_height' => $transaction['block_height'],
						'time' => $transaction['time'],
						'type' => 'creation'
					);

					$insert['balance'] = $this->getAddressNewBalance($address, $value);
					$this->mysql->insert('wallets', $insert);
				}
				//echo "+ creation " . $transactionOut['txidp'] . ' = ' .$value .  PHP_EOL;
			}

		} else {

			$a = array();
			//echo "transactions {$transaction['txid']}" . PHP_EOL;

			foreach ($transactionsIn as $transactionIn) {
				if (!isset($a[$transactionIn['address']])) {
					$a[$transactionIn['address']] = 0;
				}
				$a[$transactionIn['address']] -= $transactionIn['value'];
				//echo '- send from ' . $transactionIn['txidp'] . ' ' . $transactionIn['address'] . ' = ' . $transactionIn['value'] . PHP_EOL;
			}

			$stake = false;
			foreach ($transactionsOut as $transactionOut) {

				if (empty($transactionOut['address'])) {
					$stake = true;
					unset($transactionOut['address']);
				} else {
					if (!isset($a[$transactionOut['address']])) {
						$a[$transactionOut['address']] = 0;
					}
					$a[$transactionOut['address']] += $transactionOut['value'];
				}
				//echo '+ send to / possible stake? ' . $transactionOut['txidp'] . ' ' . $transactionOut['address']  . ' = ' . $transactionOut['value'] . PHP_EOL;
			}

			foreach ($a as $address => $value) {
				$insert = array(
					'address' => $address,
					'value' => $value,
					'txid' => $transaction['txid'],
					'block_height' => $transaction['block_height'],
					'time' => $transaction['time'],
				);

				if ($stake) {
					$insert['type'] = 'stake';
				} elseif ($value < 0) {
					$insert['type'] = 'send';
				} else {
					$insert['type'] = 'receive';
				}

				$insert['balance'] = $this->getAddressNewBalance($address, $value);
				$this->mysql->insert('wallets', $insert);
			}


		}

		//$this->mysql->completeTransaction();
	}

	private function getAddressNewBalance($address, $value) {
		$addressBalance = $this->getAddressBalance($address);
		if ($addressBalance == 0) {
			$balance = $value;
		} else {
			$balance = $addressBalance + $value;
		}
		return $balance;
	}

	public function getAddressBalance($address) {
		$sql = "SELECT balance FROM wallets WHERE address = " . $this->mysql->escape($address)
			. "ORDER BY id DESC LIMIT 1";
		$q = $this->mysql->selectRow($sql);
		$balance = 0;
		if ($q['balance'] != null) {
			$balance = $q['balance'];
		}
		return $balance;
	}

	public function buildRichList() {

		$q = $this->mysql->selectRow('SELECT outstanding from blocks ORDER BY height DESC LIMIT 1 ');
		$outstanding = (int)$q['outstanding'];

		$this->mysql->query("CREATE TABLE new_richlist LIKE richlist");

		$sql = "SELECT SUM(`value`) AS `balance`, address, MAX(`block_height`) as `block_height`, MAX(`time`) as `time` FROM wallets
			 GROUP BY address
			 ORDER BY balance DESC LIMIT 10000";

		$richList = $this->mysql->select($sql);

		foreach ($richList as $rank => $rich) {
			$insert[] = array(
				'rank' => $rank + 1,
				'address' => $rich['address'],
				'balance' => $rich['balance'],
				'block_height' => $rich['block_height'],
				'time' => $rich['time'],
				'percent' => $rich['balance'] / $outstanding * 100
			);
		}

		$this->mysql->insertMultiple('new_richlist', $insert);
		$this->mysql->startTransaction();
		$this->mysql->query("DROP TABLE richlist ");
		$this->mysql->query("CREATE TABLE richlist LIKE new_richlist");
		$this->mysql->query("INSERT INTO richlist SELECT * FROM new_richlist");
		$this->mysql->query("DROP TABLE new_richlist ");
		$this->mysql->completeTransaction();

	}

	public function getRichList($limit = 100) {

		$limit = (int) $limit;
		$richlist = $this->mysql->select("SELECT * FROM richlist LIMIT $limit");

		return $richlist;

	}

	public function primeStakes($limit) {

		$limit = (int) $limit;

		$primeStakes = $this->mysql->select("SELECT txidp as txid, asm FROM transactions_out tro
			WHERE asm LIKE 'OP_PRIME%' ORDER BY tro.id DESC LIMIT $limit");


		$txIds = array();
		foreach ($primeStakes as &$primeStake) {
			$txIds[] = $primeStake['txid'];
		}

		$rows = $this->mysql->select("SELECT t.txid, block_height, t.time, b.hash, address, b.mint AS `value`
				FROM transactions t
				JOIN blocks b ON b.height=t.block_height
				JOIN transactions_out tro ON t.`txid` = tro.`txidp` AND address IS NOT NULL
				WHERE t.txid " . $this->mysql->getInClause($txIds));

		$blocks = array();
		foreach ($rows as $row) {
			$blocks[$row['txid']] = $row;
		}

		foreach ($primeStakes as &$primeStake) {
			$primeStake['address'] = $blocks[$primeStake['txid']]['address'];
			$primeStake['hash'] = $blocks[$primeStake['txid']]['hash'];
			$primeStake['block_height'] = $blocks[$primeStake['txid']]['block_height'];
			$primeStake['time'] = $blocks[$primeStake['txid']]['time'];;
			$primeStake['OP'] = substr($primeStake['asm'], 0, 15);
			$primeStake['value'] = $blocks[$primeStake['txid']]['value'];
		}
		return $primeStakes;

	}

	public function getLatestTransactions($limit = 100) {
		$limit = (int) $limit;

		$transactions = $this->mysql->select("SELECT * FROM wallets w ORDER BY time DESC LIMIT $limit");
		return $transactions;

	}

	public function getOutstandingDataPoints($limit) {

		$limit = (int) $limit;

		$blocks = $this->mysql->select("SELECT `timestamp`, outstanding,
		DATE_FORMAT(FROM_UNIXTIME(TIMESTAMP), '%m %d %y %h %m') AS points
		FROM blocks GROUP BY points ORDER BY `height`   LIMIT " . (int)$limit);
		//$dataPoints[] = "[1418361000, 0] \n";
		foreach ($blocks as $block) {
			$dataPoint = array(
				'time' => $block['timestamp'] *= 1000, // convert from Unix timestamp to JavaScript time,
				'value' => $block['outstanding']
			);
			$dataPoints[] = "[{$dataPoint['time']}, {$dataPoint['value']}] \n";
		}


		return $dataPoints;
	}

	public function getDifficultyDataPoints($limit) {

		$limit = (int) $limit;

		$blocks = $this->mysql->select("SELECT `timestamp`, difficulty,
		DATE_FORMAT(FROM_UNIXTIME(TIMESTAMP), '%m %d %y %h %m') AS points
		FROM blocks
		-- GROUP BY points
		ORDER BY `time`  LIMIT " . (int)$limit);
		//$dataPoints[] = "[1418361000, 0] \n";
		foreach ($blocks as $block) {
			$dataPoint = array(
				'time' => $block['timestamp'] *= 1000, // convert from Unix timestamp to JavaScript time,
				'difficulty' => $block['difficulty']
			);
			$dataPoints[] = "[{$dataPoint['time']}, {$dataPoint['difficulty']}] \n";
		}


		return $dataPoints;
	}
} 