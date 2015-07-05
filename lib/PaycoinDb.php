<?php
/**
 * @author John <john@paycoin.com>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */
namespace lib;

/**
 * Class PaycoinDb
 * @package lib
 */
class PaycoinDb {

	const BC_SCALE = 6;
	public $mysql;

	public function __construct() {
		$this->mysql = Mysql::getInstance();
	}

	public function getBlockByHeight($blockHeight) {
		$blockHeight = (int)$blockHeight;
		$block = $this->mysql->selectRow("SELECT * FROM blocks b WHERE `height` = $blockHeight ");

		return $block;
	}

	public function getLatestBlocks($limit, $height = 0, $cache = 30) {
		if ($limit > 1000) {
			$cache = false;
		}
		$sql = "SELECT * FROM blocks b ";
		$sortOrder = 'DESC';
		if ($height > 0) {
			$sql .= " WHERE `height` >= " .(int)$height;
			$sortOrder = 'ASC';
		}
		$sql .= " ORDER by `height` {$sortOrder} LIMIT " . (int)$limit;
		$blocks = $this->mysql->select($sql, $cache);

		return $blocks;
	}

	public function getBlockByHash($hash) {

		$block = $this->mysql->selectRow("SELECT * FROM blocks b WHERE `hash` = " . $this->mysql->escape($hash), 30);

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
		$blocks = $this->mysql->select($sql, 30);
		foreach ($blocks as $k => $v) {
			$blocks[$k]['raw'] = unserialize($v['raw']);
		}

		return $blocks;
	}

	public function getTransactionsOut($txid) {

		$sql = "SELECT  * from transactions_out WHERE `txidp` = " . $this->mysql->escape($txid);
		$transactions = $this->mysql->select($sql, 30);

		return $transactions;
	}

	public function getTransactionsIn($txid) {

		$sql = "SELECT  * from transactions_in WHERE `txidp` = " . $this->mysql->escape($txid);
		$transactions = $this->mysql->select($sql, 30);

		return $transactions;
	}

	/**
	 * Used to determine Redeemed in
	 * @param $txid
	 * @return array
	 */
	public function getTransactionIn($txid) {

		$sql = "SELECT  * from transactions_in WHERE `txid` = " . $this->mysql->escape($txid);
		$transactions = $this->mysql->select($sql, 30);

		return $transactions;
	}

	public function getTransaction($txId) {

		$transaction = $this->mysql->selectRow("
			SELECT t.*, b.flags, b.hash FROM transactions t
			 JOIN blocks b on b.height = t.block_height
			WHERE t.txid = " . $this->mysql->escape($txId), 30);

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

			$totalValue = bcadd($vOut['valueTotal'], $totalValue, self::BC_SCALE);
			$totalValueIn = bcadd($vIn['valueTotal'], $totalValueIn, self::BC_SCALE);

			$txFee = bcsub($totalValue, $totalValueIn, self::BC_SCALE);
			$txFee = bcsub($txFee, $block['mint'], self::BC_SCALE);

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
				$valueTotal = bcadd($insert['value'], $valueTotal, self::BC_SCALE);
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
					$valueTotal = bcadd($value, $valueTotal, self::BC_SCALE);
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

		$r = $this->mysql->selectRow("SELECT MAX(`height`) as `height` FROM `blocks`", false);
		if ($r['height'] == NULL) {
			$return = 0;
		} else {
			$return = $r['height'];
		}

		return $return;
	}

	public function buildDb($startBlockHeight, $endBlockHeight) {
		/**
		 * http://192.168.10.10/block/009a2d16f34b49318e2e78f12a7b64816cd064d7b68abc862b7377f6576919ab
		 * Created	0.848203 XPY = wrong.. txfee not removed
		 *
		 * *possible* Outstanding is not calculating correctly...
		 * 1-138400 = ok
		 * 138400-139000 =
		 ** 138401 is where it starts....
		 **  at block 140000 a problem.. 15,619,171.776635 XPY byt should be 15,619,171.7766351 XPY
		 */
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


			$outstanding = bcadd($block['mint'], $outstanding, self::BC_SCALE);

			if (count($block['tx']) > 1) {
				$txFees = bcsub($blockInsert['valueout'], bcadd($blockInsert['valuein'], $blockInsert['mint'], self::BC_SCALE), self::BC_SCALE);
				$outstanding = bcadd($outstanding, $txFees, self::BC_SCALE);
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


		//rate limit.
		$rateLimit = 10;
		$rateLimitSeconds = 60;
		$rateLimiter = new RateLimiter($_SERVER['REMOTE_ADDR'] . ':search', $rateLimitSeconds);

		if (!$rateLimiter->allow($rateLimit)) {
			throw new Exceptions\RateLimitException('Rate limit exceeded.');
		}

		$maxResults = 10;
		$maxPerItemResults = 5;
		$return = array();

		//check if address
		if (strlen($q) == 34) {
			$return['Address'][] = '/address/' . urlencode($q);
		}
		//@todo sql for address.

		//check if block height
		if (is_string($q)) {

			$tags = $this->mysql->select("SELECT `address`, `tag`, `verified` FROM address_tags WHERE `tag` LIKE "
				. $this->mysql->escape($q . '%'));
			if ($tags != false) {
				foreach ($tags as $i => $tag) {
					$return['Tag'][ $tag['verified'] . ':' . $tag['tag'] . ':' . $i] = '/address/' . urlencode($tag['address']);
				}
			}
			//var_dump($return); exit;
		}

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
	public function getAddressTag($address) {
		$sql = "SELECT `tag`, `url`, `verified` FROM address_tags"
			. " WHERE address = " . $this->mysql->escape($address);
		$tagRow = $this->mysql->selectRow($sql, 60);
		if ($tagRow == null) {
			return null;
		}
		if ($tagRow['verified'] == 3) { //dispute
			return null;
		}
		return $tagRow;

	}
	public function disputeAddressTag($address) {

		$sql = "UPDATE address_tags SET verified=3 WHERE address = " . $this->mysql->escape($address);
		$this->mysql->query($sql);

	}

	public function getAddressInformation($address, $limit = 100000) {

		$sql = "SELECT a.*, rl.rank, adt.`tag`, adt.`url`, adt.`verified` FROM addresses a "
			. " LEFT JOIN richlist rl on rl.address= a.address"
			. " LEFT JOIN address_tags adt ON adt.address = a.address"
			. " WHERE a.address = " . $this->mysql->escape($address)
			. " ORDER BY a.id DESC";
		if ($limit > 0 && $limit != 'all') {
			$sql .= " LIMIT " . (int)$limit;
		}

		$transactions = $this->mysql->select($sql, 30);

		$return['transactions'] = $transactions;
		$return['address'] = $address;
		if (isset($transactions[0]['tag'])) {
			$return['addressTag'] = array(
				'tag' => $transactions[0]['tag'],
				'url' => $transactions[0]['url'],
				'verified' => $transactions[0]['verified']
			);
		}

		$last = current($transactions);
		$return['rank'] = $last['rank'];
		$return['balance'] = $last['balance'];


		$sql = "SELECT `address`, `type`, SUM(`value`) as `sum`, COUNT(*) as `transactions` FROM addresses WHERE address = " . $this->mysql->escape($address)
			.  "GROUP BY `address`, `type` ";

		$totals = $this->mysql->select($sql, 30);
		$return['totalTransactions'] = 0;

		foreach ($totals as $total) {
			if ($total['type'] == 'receive') {
				$return['totalInValue'] = $total['sum'];
				$return['totalInTransactions'] = $total['transactions'];
			} if ($total['type'] == 'send' ) {
				$return['totalOutValue'] = str_replace('-', '',$total['sum']);
				$return['totalOutTransactions'] = $total['transactions'];
			} elseif ($total['type'] == 'stake') {
				$return['totalStakeValue'] = $total['sum'];
				$return['totalStakeTransactions'] = $total['transactions'];
			} elseif ($total['type'] == 'creation') {
				$return['totalCreationValue'] = $total['sum'];
				$return['totalCreationTransactions'] = $total['transactions'];
			}
			$return['totalTransactions'] += $total['transactions'];

		}
		return $return;

	}

	public function buildWalletDb() {

		$limit = 1000;

		$q = $this->mysql->selectRow('SELECT id FROM transactions WHERE txid = (SELECT txid FROM addresses ORDER BY id DESC LIMIT 1)');
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
	 * @param array $transaction
	 */
	public function addTransactionToAddress(array $transaction) {

		$sql = "SELECT * FROM transactions_in WHERE value IS NOT NULL AND txidp = " . $this->mysql->escape($transaction['txid']);
		$transactionsIn = $this->mysql->select($sql);

		$sql = "SELECT * FROM transactions_out WHERE txidp = " . $this->mysql->escape($transaction['txid']);
		$transactionsOut = $this->mysql->select($sql);

		if (count($transactionsIn) == 0) {
			//only outputs.. creation?
			$this->addAddressCreations($transaction, $transactionsOut);
		} else {
			$this->addAddressTransactions($transaction, $transactionsIn, $transactionsOut);

		}

	}

	private function updateAddresses(array $transaction, array $addressValueMap, $type = null, $stake=false) {

		if (count($addressValueMap) <= 0) {
			return;
		}
		foreach ($addressValueMap as $address => $value) {
			if ($type == null) {
				if ($stake) {
					$type = 'stake';
				} elseif ($value < 0) {
					$type = 'send';
				} else {
					$type = 'receive';
				}
			}
			$insert = array(
				'address' => $address,
				'value' => $value,
				'txid' => $transaction['txid'],
				'block_height' => $transaction['block_height'],
				'time' => $transaction['time'],
				'type' => $type
			);

			$insert['balance'] = $this->getAddressNewBalance($address, $value);
			$this->mysql->insert('addresses', $insert);
		}
	}

	private function addAddressTransactions($transaction, $transactionsIn, $transactionsOut) {

		$addressValueMap = array();
		//echo "transactions {$transaction['txid']}" . PHP_EOL;

		foreach ($transactionsIn as $transactionIn) {
			if (!isset($addressValueMap[$transactionIn['address']])) {
				$addressValueMap[$transactionIn['address']] = 0;
			}
			$addressValueMap[$transactionIn['address']] -= $transactionIn['value'];
			//echo '- send from ' . $transactionIn['txidp'] . ' ' . $transactionIn['address'] . ' = ' . $transactionIn['value'] . PHP_EOL;
		}

		$stake = false;
		foreach ($transactionsOut as $transactionOut) {

			if (empty($transactionOut['address'])) {
				$stake = true;
				unset($transactionOut['address']);
			} else {
				if (!isset($addressValueMap[$transactionOut['address']])) {
					$addressValueMap[$transactionOut['address']] = 0;
				}
				$addressValueMap[$transactionOut['address']] += $transactionOut['value'];
			}
			//echo '+ send to / possible stake? ' . $transactionOut['txidp'] . ' ' . $transactionOut['address']  . ' = ' . $transactionOut['value'] . PHP_EOL;
		}

		foreach ($addressValueMap as $address => $value) {
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
			$this->mysql->insert('addresses', $insert);
		}

	}

	private function addAddressCreations($transaction, $transactionsOut) {

		$addressValueMap = array();
		$value = 0;

		foreach ($transactionsOut as $transactionOut) {
			if (empty($transactionOut['address'])) {
				continue;
			}
			if (!isset($addressValueMap[$transactionOut['address']])) {
				$addressValueMap[$transactionOut['address']] = 0;
			}
			if ($transactionOut['value'] > 0) {
				$value += $transactionOut['value'];
				$addressValueMap[$transactionOut['address']] = $value;
			}

		}

		$this->updateAddresses($transaction, $addressValueMap, 'creation');

	}
	
	
//	public function addTransactionToAddress_old(array $transaction) {
//
//		//$this->mysql->startTransaction();
//
//		$sql = "SELECT * FROM transactions_in WHERE value IS NOT NULL AND txidp = " . $this->mysql->escape($transaction['txid']);
//		$transactionsIn = $this->mysql->select($sql);
//
//		$sql = "SELECT * FROM transactions_out WHERE txidp = " . $this->mysql->escape($transaction['txid']);
//		$transactionsOut = $this->mysql->select($sql);
//
//		if (count($transactionsIn) == 0) { //only outputs.. creation?
//			$a = array();
//			$value = 0;
//
//			foreach ($transactionsOut as $transactionOut) {
//				if (empty($transactionOut['address'])) {
//					continue;
//				}
//				if (!isset($a[$transactionOut['address']])) {
//					$a[$transactionOut['address']] = 0;
//				}
//				if ($transactionOut['value'] > 0) {
//					$value += $transactionOut['value'];
//					$a[$transactionOut['address']] = $value;
//				}
//
//			}
//
//			if (count($a) > 0) {
//				//echo "creation {$transaction['txid']}" . PHP_EOL;
//				foreach ($a as $address => $value) {
//					$insert = array(
//						'address' => $address,
//						'value' => $value,
//						'txid' => $transaction['txid'],
//						'block_height' => $transaction['block_height'],
//						'time' => $transaction['time'],
//						'type' => 'creation'
//					);
//
//					$insert['balance'] = $this->getAddressNewBalance($address, $value);
//					$this->mysql->insert('addresses', $insert);
//				}
//				//echo "+ creation " . $transactionOut['txidp'] . ' = ' .$value .  PHP_EOL;
//			}
//
//		} else {
//
//			$a = array();
//			//echo "transactions {$transaction['txid']}" . PHP_EOL;
//
//			foreach ($transactionsIn as $transactionIn) {
//				if (!isset($a[$transactionIn['address']])) {
//					$a[$transactionIn['address']] = 0;
//				}
//				$a[$transactionIn['address']] -= $transactionIn['value'];
//				//echo '- send from ' . $transactionIn['txidp'] . ' ' . $transactionIn['address'] . ' = ' . $transactionIn['value'] . PHP_EOL;
//			}
//
//			$stake = false;
//			foreach ($transactionsOut as $transactionOut) {
//
//				if (empty($transactionOut['address'])) {
//					$stake = true;
//					unset($transactionOut['address']);
//				} else {
//					if (!isset($a[$transactionOut['address']])) {
//						$a[$transactionOut['address']] = 0;
//					}
//					$a[$transactionOut['address']] += $transactionOut['value'];
//				}
//				//echo '+ send to / possible stake? ' . $transactionOut['txidp'] . ' ' . $transactionOut['address']  . ' = ' . $transactionOut['value'] . PHP_EOL;
//			}
//
//			foreach ($a as $address => $value) {
//				$insert = array(
//					'address' => $address,
//					'value' => $value,
//					'txid' => $transaction['txid'],
//					'block_height' => $transaction['block_height'],
//					'time' => $transaction['time'],
//				);
//
//				if ($stake) {
//					$insert['type'] = 'stake';
//				} elseif ($value < 0) {
//					$insert['type'] = 'send';
//				} else {
//					$insert['type'] = 'receive';
//				}
//
//				$insert['balance'] = $this->getAddressNewBalance($address, $value);
//				$this->mysql->insert('addresses', $insert);
//			}
//
//
//		}
//
//		//$this->mysql->completeTransaction();
//	}

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
		$sql = "SELECT balance FROM addresses WHERE address = " . $this->mysql->escape($address)
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

		$sql = "SELECT SUM(`value`) AS `bal`, address, MAX(`block_height`) as `block_height`, MAX(`time`) as `time` FROM addresses
			 GROUP BY address
			 HAVING bal > 0
			 ORDER BY bal DESC LIMIT 100000";

		$richList = $this->mysql->select($sql);

		foreach ($richList as $rank => $rich) {
			$insert[] = array(
				'rank' => $rank + 1,
				'address' => $rich['address'],
				'balance' => $rich['bal'],
				'block_height' => $rich['block_height'],
				'time' => $rich['time'],
				'percent' => $rich['bal'] / $outstanding * 100
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
		$richlist = $this->mysql->select("SELECT * FROM richlist LIMIT $limit", 60);

		return $richlist;

	}

	public function getPossibleBidders() {

		$bidders = $this->mysql->selectRow("SELECT COUNT(*) as bidders FROM richlist WHERE balance > " . PRIME_BID_AMOUNT);
		return $bidders['bidders'];

	}

	public function getPrimeBids($limit = 50) {

		$primeBid = PRIME_BID_AMOUNT;
		$limit = (int) $limit;
		$bids = $this->mysql->select("SELECT * FROM address_tags tags JOIN richlist r ON r.address = tags.address
			WHERE verified = 1 AND tag LIKE 'primebid%' and balance > $primeBid ORDER BY balance DESC LIMIT $limit ", 60);

		$rank = 1;
		foreach ($bids as &$bid) {
			$bid['bidrank'] = $rank;
			$bid['balance'] = $this->getAddressBalance($bid['address']);
			$bid['bid'] = $bid['balance'] - $primeBid;
			$rank++;
		}



		return $bids;

	}


	public function primeStakes($limit) {

		$limit = (int) $limit;

		$primeStakes = $this->mysql->select("SELECT txidp as txid, asm FROM transactions_out tro
			WHERE asm LIKE 'OP_PRIME%' ORDER BY tro.id DESC LIMIT $limit", 60);


		$txIds = array();
		foreach ($primeStakes as &$primeStake) {
			$txIds[] = $primeStake['txid'];
		}
		if (count($txIds) == 0) {
			return array();
		}
		$rows = $this->mysql->select("SELECT t.txid, block_height, t.time, b.hash, address, b.mint AS `value`
				FROM transactions t
				JOIN blocks b ON b.height=t.block_height
				JOIN transactions_out tro ON t.`txid` = tro.`txidp` AND address IS NOT NULL
				WHERE t.txid " . $this->mysql->getInClause($txIds), 60);

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

	public function getLatestAddressTransactions($limit = 100) {
		$limit = (int) $limit;

		$transactions = $this->mysql->select("SELECT * FROM addresses ORDER BY time DESC LIMIT $limit", 30);
		return $transactions;

	}

	public function getLatestTransactions($limit = 100) {
		$limit = (int) $limit;

		$transactions = $this->mysql->select("SELECT * FROM transactions ORDER BY id DESC  LIMIT $limit", 30);
		return $transactions;

	}

	public function getLatestBlockTransactions($limit = 100) {
		$limit = (int) $limit;

		$transactions = $this->mysql->select("SELECT  SUM(b.mint) - SUM(t.txFee) AS txFee, t.block_height FROM transactions t JOIN blocks b ON b.height = t.`block_height`
			GROUP BY t.block_height ORDER BY t.id DESC  LIMIT $limit", 300);
		return $transactions;

	}

	public function getOutstandingDataPoints($limit) {

		$limit = (int) $limit;

		$blocks = $this->mysql->select("SELECT `timestamp`, outstanding,
		DATE_FORMAT(FROM_UNIXTIME(TIMESTAMP), '%m %d %y %h %m') AS points
		FROM blocks GROUP BY points ORDER BY `timestamp` ASC  LIMIT " . (int)$limit, 3500);
		//$dataPoints[] = "[1418361000, 0] \n";
		$dataPoints = array();
		foreach ($blocks as $block) {
			$dataPoint = array(
				'time' => $block['timestamp'] *= 1000, // convert from Unix timestamp to JavaScript time,
				'value' => $block['outstanding']
			);
			$dataPoints[] = "[{$dataPoint['time']}, {$dataPoint['value']}] \n";
		}


		return $dataPoints;
	}


	public function getTransactionsPerBlockDataPoints($limit) {

		$limit = (int) $limit;

		$blocks = $this->mysql->select("SELECT `timestamp`, transactions
		FROM blocks
		ORDER BY `timestamp`  LIMIT " . (int)$limit, 600);
		//$dataPoints[] = "[1418361000, 0] \n";
		foreach ($blocks as $block) {
			$dataPoint = array(
				'time' => $block['timestamp'] *= 1000, // convert from Unix timestamp to JavaScript time,
				'transactions' => $block['transactions']
			);
			$dataPoints[] = "[{$dataPoint['time']}, {$dataPoint['transactions']}] \n";
		}


		return $dataPoints;
	}

	public function getDifficultyDataPoints($limit) {

		$limit = (int) $limit;

		$blocks = $this->mysql->select("SELECT `timestamp`, difficulty,
		DATE_FORMAT(FROM_UNIXTIME(TIMESTAMP), '%m %d %y %h %i') AS points
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

	public function addTagToAddress($address, $tag, $url = null, $verified = 0) {
		$insert = array(
			'address' => $address,
			'tag' => $tag,
			'time_created' => time(),
			'verified' => $verified,
		);
		$update = array(
			'tag' => $tag,
			'time_updated' => time(),
			'verified' => $verified,
		);
		if ($url != null) {
			$insert['url'] = $url;
			$update['url'] = $url;
		}
		return $this->mysql->insert('address_tags', $insert, false, $update);
	}



	public function getRichListDistribution() {
		$sql = "SELECT 10 AS top, SUM(balance) AS holdings FROM (SELECT balance FROM richlist WHERE `balance` > 0 ORDER BY balance DESC LIMIT 10) AS result
					UNION
					SELECT 100, SUM(balance) FROM (SELECT balance FROM richlist WHERE `balance` > 0 ORDER BY balance DESC LIMIT 100) AS result
					UNION
					SELECT 1000, SUM(balance) FROM (SELECT balance FROM richlist WHERE `balance` > 0 ORDER BY balance DESC LIMIT 1000) AS result
					UNION
					SELECT COUNT(*),SUM(balance) FROM richlist";
		$distribution = $this->mysql->select($sql, 60);
		return  $distribution;
	}

	public function getAddressTagMap($addresses) {
		if (count($addresses) == 0) {
			return array();
		}
		$sql = "SELECT * FROM address_tags WHERE address " . $this->mysql->getInClause($addresses);
		$map = $this->mysql->select($sql, 60);

		$map = array_column($map, null, 'address');

		return $map;

	}

	public function updateNetworkInfo() {

		$paycoin = new PaycoinRPC('dnsseed');
		$peers = $paycoin->getPeerInfo();
		if (count($peers) > 0) {
			$this->updatePeers($peers);
		}

//		$paycoin = new PaycoinRPC('jwrb');
//		$peers = $paycoin->getPeerInfo();
//		var_dump($peers);
	}

	private function updatePeers($peers) {
		$maxMind = new MaxMind();
		foreach ($peers as $peer) {

			$insert = $peer;
			$update = $peer;

			list($ip, $post) = explode(':', $peer['addr']);
			$geoInfo = $maxMind->getGeoInfo($ip);
			sleep(.5);
			$insert['country_code'] = $geoInfo['countryCode'];
			$insert['country_name'] = $geoInfo['countryName'];
			$insert['state'] = $geoInfo['state'];
			$insert['city'] = $geoInfo['city'];

			$update['country_code'] = $geoInfo['countryCode'];
			$update['country_name'] = $geoInfo['countryName'];
			$update['state'] = $geoInfo['state'];
			$update['city'] = $geoInfo['city'];

			$this->mysql->insert('network', $insert, false, $update);

		}
	}

	public function getNetwork() {
		$since = mktime(0, 0, 0) - (24 * 60 * 60);

		$sql = "SELECT COUNT(*) as connections, network.*  FROM network
			WHERE lastsend > $since OR lastrecv > $since GROUP BY subver order by connections desc";


		$subVersions = $this->mysql->select($sql, 60);
		$totalConnections = 0;
		foreach ($subVersions as &$row) {
			$totalConnections += $row['connections'];
		}
		foreach ($subVersions as &$row) {
			$row['share'] = round($row['connections']/$totalConnections * 100, 1);
		}

		$graphData = array_column($subVersions, 'share', 'subver');

		return array(
			'graphData' => $graphData,
			'totalConnections' => $totalConnections,
			'subVersions' => $subVersions
		);

	}

	public function getNetworkByCity($limit) {
		$since = mktime(0, 0, 0) - (24 * 10 * 60 * 60);

		$sql = "SELECT COUNT(*) as connections, network.*  FROM network
			WHERE lastsend > $since OR lastrecv > $since GROUP BY country_code, city, state order by connections desc limit $limit";

		$network = $this->mysql->select($sql, 60);

		return $network;

	}


	public function getNetworkMapData() {
		$since = mktime(0, 0, 0) - (24 * 10 * 60 * 60);

		$sql = "SELECT COUNT(*) as connections, LOWER(country_code) AS country FROM network
			WHERE lastsend > $since OR lastrecv > $since GROUP BY country_code order by connections desc";

		$data = $this->mysql->select($sql, 60);
		return array_column($data, 'connections', 'country');

	}

	public function getNodes($subver) {
		$since = mktime(0, 0, 0) - (24 * 10 * 60 * 60);
		$sql = "SELECT SUBSTRING_INDEX(addr, ':', 1) AS addr FROM network
			WHERE subver = " . $this->mysql->escape($subver) . " AND lastsend > $since OR lastrecv > $since GROUP BY addr";

		return $this->mysql->select($sql, 60);

	}

} 