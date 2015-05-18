<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 5/15/15
 * Time: 10:17 PM
 */

namespace controllers;
use PP\PaycoinRPC;
use PP\Helper;
use Symfony\Component\Config\Definition\Exception\Exception;
use PP\Mysql;

class Cli extends Controller {

	public function buildDatabase() {


		//@todo correct value out calculations
		//@todo add outstanding calculations
		//@todo refactor.. Move out of controller...


		$batchInsertAt = 100;


		echo 'Building Database' . PHP_EOL;

		$paycoin = new PaycoinRPC();
		$mysql = new Mysql();

		$startBlockHeight = $this->getLastBlockInDb();
		$startBlockHeight = (int)$startBlockHeight;
		$startBlockHeight++;
//		$startBlockHeight = 2500;

		$endBlockHeight = $paycoin->getBlockCount();

		if ($startBlockHeight == $endBlockHeight) {
			echo "Caught up.  Last block was $endBlockHeight" . PHP_EOL;
			return;
		} else {
			echo "Catching up with blockchain  $startBlockHeight => $endBlockHeight" . PHP_EOL;
		}

		for ($i = $startBlockHeight; $i < $endBlockHeight; $i++) {

			$blockHash = $paycoin->getBlockHash($i);
			$block = $paycoin->getBlock($blockHash);
			//echo "Block Height {$block['height']}" . PHP_EOL;
			$blockInsert[$i] = array(
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
				'transactions' => count($block['tx'])
			);
			$totalValueOut = (float)0;

			foreach ($block['tx'] as $txId) {
				try {

					$raw = $paycoin->getRawTransaction($txId);
					$transaction = $paycoin->decodeRawTransaction($raw);

					$transactionInsert[] = array(
						'txid' => $transaction['txid'],
						'version' => $transaction['version'],
						'time' => $transaction['time'],
						'locktime' => $transaction['locktime'],
						'block_height' => $block['height'],
						'raw' => serialize($transaction),

					);
					foreach ($transaction['vin'] as $vin) {

							$transactionVinInsert[] = array(
								'txid' => $transaction['txid'],
								'coinbase' => Helper::getValue($vin, 'coinbase'),
								'sequence' => Helper::getValue($vin, 'sequence'),
								'vout' => isset($vin['vout']) ? $vin['vout'] : null,
								'asm' => isset($vin['scriptSig']['asm']) ? $vin['scriptSig']['asm'] : null,
								'hex' => isset($vin['scriptSig']['hex']) ? $vin['scriptSig']['hex'] : null
							);


					}

					foreach ($transaction['vout'] as $vout) {
						$totalValueOut += $vout['value'];
						$transactionVoutInsert[] = array(
							'txid' => $transaction['txid'],
							'value' => $vout['value'],
							'n' => $vout['n'],
							'asm' => isset($vout['scriptPubKey']['asm']) ? $vout['scriptPubKey']['asm'] : null,
							'hex' => isset($vout['scriptPubKey']['hex']) ? $vout['scriptPubKey']['hex'] : null,
							'reqSigs' => isset($vout['scriptPubKey']['reqSigs']) ? $vout['scriptPubKey']['reqSigs'] : null,
							'type' => $vout['scriptPubKey']['type'],
							'address' => isset($vout['scriptPubKey']['addresses'][0]) ? $vout['scriptPubKey']['addresses'][0] : null,
						);
						if (isset($vout['scriptPubKey']['addresses'][0])) {
							//@todo somehow this is missing some...
							//ie: PCDvqdVsF6hX1vdi8QzD9VwLsKUMUk1DDk

							$addresses[$vout['scriptPubKey']['addresses'][0]] =	$vout['scriptPubKey']['addresses'][0];

						}

					}

				} catch (\Exception $e) {
					echo 'nope..' . PHP_EOL;
				}
			}

			$blockInsert[$i]['valueout'] = $totalValueOut;

//var_dump($totalValueOut); exit;

			if ($i > 1 && $i%$batchInsertAt == 0) {
				echo "Inserting {$batchInsertAt} blocks" . PHP_EOL;

				$mysql->startTransaction();
				$mysql->insertMultiple('blocks', array_keys(current($blockInsert)), $blockInsert, true);
				$mysql->insertMultiple('transactions', array_keys($transactionInsert[0]), $transactionInsert, true);
				$mysql->insertMultiple('transactions_in', array_keys($transactionVinInsert[0]), $transactionVinInsert, true);
				$mysql->insertMultiple('transactions_out', array_keys($transactionVoutInsert[0]), $transactionVoutInsert, true);
				$mysql->completeTransaction();

				foreach ($addresses as $address) {
					$walletInsert[] = array(
						'address' => $address
					);
				}

				$mysql->insertMultiple('wallets', array_keys($walletInsert[0]), $walletInsert, true);
				$walletInsert = $addresses = $transactionInsert = $transactionVinInsert = $transactionVoutInsert = $blockInsert = array();

			}

		}

		if (count($blockInsert) > 0) {
			echo 'Inserting remaining blocks' . PHP_EOL;
			$mysql->insertMultiple('blocks', array_keys(current($blockInsert)), $blockInsert, true);
			$mysql->insertMultiple('transactions', array_keys($transactionInsert[0]), $transactionInsert, true);
			$mysql->insertMultiple('transactions_in', array_keys($transactionVinInsert[0]), $transactionVinInsert, true);
			$mysql->insertMultiple('transactions_out', array_keys($transactionVoutInsert[0]), $transactionVoutInsert, true);
		}

		echo "Complete" . PHP_EOL;

	}

	public function getLastBlockInDb() {
		$mysql = mysqli_connect('127.0.0.1', 'root', '', 'pp');
		$r = $mysql->query("SELECT MAX(`height`) FROM `blocks`");
		$q = $r->fetch_array();
		return $q[0];
	}


} 