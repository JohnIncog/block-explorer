<?php

namespace PP;


class PaycoinRPC {

	public $paycoind;

	public function __construct() {

		$this->paycoind = new \jsonRPCClient('http://user:qwerty@127.0.0.1:8332/');
	}

	public function getInfo() {
		return $this->paycoind->getinfo();
	}

	public function help() {
		return $this->paycoind->help();
	}

	public function getBlockHash($blockHeight) {
		return $this->paycoind->getblockhash($blockHeight);
	}

	public function getBlock($blockHash) {
		return $this->paycoind->getblock($blockHash);
	}

	public function getTransaction($txId) {
		return $this->decodeRawTransaction($this->getRawTransaction($txId));
	}

	public function decodeRawTransaction($hex) {
		return $this->paycoind->decoderawtransaction($hex);
	}

	public function getTransactionHex($txId) {
		return $this->getRawTransaction($txId);
	}

	public function getRawTransaction($TxHex) {
		return $this->paycoind->getrawtransaction($TxHex);
	}

	public function getBlockCount() {
		return $this->paycoind->getblockcount();
	}

	public function getLatestBlockHeight() {
		return $this->paycoind->getblockcount();

	}
	public function getLastBlocks() {
		$lastBlock = $this->paycoind->getblockcount();
		$blocks = array();
		if ($lastBlock > 10) {
			for ($i=0; $i<10; $i++) {
				$blockHeight = $lastBlock - $i;
				$blocks[$blockHeight]['hash'] = $this->getBlockHash($blockHeight);
				$blocks[$blockHeight]['details'] = $this->getBlock($blocks[$blockHeight]['hash']);
			}

		}
		return $blocks;
	}

//	public function getTransaction($txId) {
//		$raw = $this->paycoind->getrawtransaction($txId);
//		return $this->paycoind->decoderawtransaction($raw);
//	}
} 