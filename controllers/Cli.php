<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 5/15/15
 * Time: 10:17 PM
 */

namespace controllers;
use PP\PaycoinDb;
use PP\PaycoinRPC;
use PP\Helper;
use Symfony\Component\Config\Definition\Exception\Exception;
use PP\Mysql;

class Cli extends Controller {

	public function buildDatabase() {

		//@todo add outstanding calculations
		//@todo refactor.. Move out of controller...
		//@todo scrape wallets.

		echo 'Building Database' . PHP_EOL;

		$paycoinRPC = new PaycoinRPC();
		$paycoinDb = new PaycoinDb();

		$startBlockHeight = $paycoinDb->getLastBlockInDb();
		$startBlockHeight = (int)$startBlockHeight;
		$startBlockHeight++;

		$endBlockHeight = $paycoinRPC->getBlockCount();

		if ($startBlockHeight == $endBlockHeight) {
			echo "Caught up.  Last block was $endBlockHeight" . PHP_EOL;
			return;
		} else {
			echo "Catching up with blockchain  $startBlockHeight => $endBlockHeight" . PHP_EOL;
		}

		//@todo move this...

		$paycoinDb->buildDb($startBlockHeight, $endBlockHeight);

		echo "Complete" . PHP_EOL;

	}



} 