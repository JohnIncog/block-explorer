<?php

namespace controllers;
use lib\PaycoinDb;
use lib\PaycoinRPC;

class Cli extends Controller {

	const LOCK_FILE = "/tmp/clibuildDatabase.lock";

	public function buildDatabase() {


		if (!$this->tryLock()) {
			die("Already running.\n");
		}
		register_shutdown_function('unlink', self::LOCK_FILE);

		echo 'Building Database' . PHP_EOL;

		$paycoinRPC = new PaycoinRPC();
		$paycoinDb = new PaycoinDb();

		$startBlockHeight = $paycoinDb->getLastBlockInDb();
		$startBlockHeight = (int)$startBlockHeight;


		$endBlockHeight = $paycoinRPC->getBlockCount();

		if ($startBlockHeight != 0 && $startBlockHeight == $endBlockHeight) {
			echo "Caught up.  Last block was $endBlockHeight" . PHP_EOL;
			return;
		} else {
			echo "Catching up with blockchain  $startBlockHeight => $endBlockHeight" . PHP_EOL;
		}

		//@todo move this...
		$startBlockHeight++;
		$paycoinDb->buildDb($startBlockHeight, $endBlockHeight);

		echo "Complete" . PHP_EOL;

	}

	public function buildWalletDatabase() {

		$paycoinDb = new PaycoinDb();
		echo "Building wallet database" . PHP_EOL;
		$paycoinDb->buildWalletDb();

	}

	public function buildRichList() {

		$paycoinDb = new PaycoinDb();
		echo "Building rich list" . PHP_EOL;
		$paycoinDb->buildRichList();

	}

	private function tryLock() {

		if (@symlink("/proc/" . getmypid(), self::LOCK_FILE) !== FALSE) # the @ in front of 'symlink' is to suppress the NOTICE you get if the LOCK_FILE exists
			return true;

		# link already exists
		# check if it's stale
		if (is_link(self::LOCK_FILE) && !is_dir(self::LOCK_FILE)) {
			unlink(self::LOCK_FILE);
			# try to lock again
			return $this->tryLock();
		}

		return false;
	}



} 