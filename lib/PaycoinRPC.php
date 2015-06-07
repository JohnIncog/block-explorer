<?php

namespace lib;


use JsonSchema\Uri\UriRetriever;

class PaycoinRPC {

	public $paycoind;

	public function __construct() {
		include(__DIR__ . '/../conf/config.php');
		$rpcUrl = 'http://' . $config['paycoind']['rpcuser'] . ':' . $config['paycoind']['rpcpassword'] .
			'@' . $config['paycoind']['rpchost'] . ':' . $config['paycoind']['rpcport'] . '/';
		$this->paycoind = new \jsonRPCClient($rpcUrl);
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

	public function verifySignedMessage($address, $message, $signature) {

		try {

			$result = $this->paycoind->verifymessage($address, $message, $signature);
			return $result;

		} catch (\Exception $e) {
			return $e->getMessage();
		}

	}

} 