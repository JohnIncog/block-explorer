<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 5/16/15
 * Time: 11:22 PM
 */

namespace PP;
use PP\Mysql;

class PaycoinDb {

	public function getBlockByHeight($blockHeight) {
		$mysql = new Mysql();
		$blockHeight = (int)$blockHeight;
		$block = $mysql->select("SELECT * FROM blocks b WHERE `height` = $blockHeight ");

		return $block[0];
	}

	public function getLatestBlocks($limit) {
		$mysql = new Mysql();
		$blocks = $mysql->select("SELECT * FROM blocks b ORDER by `height` DESC LIMIT " . (int)$limit);

		return $blocks;
	}

	public function getBlockByHash($hash) {
		$mysql = new Mysql();
		$block = $mysql->select("SELECT * FROM blocks b WHERE `hash` = " . $mysql->escape($hash));

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
		$mysql = new Mysql();
		$sql = "SELECT * FROM transactions t WHERE `block_height` = " . $mysql->escape($blockHeight);
		$blocks = $mysql->select($sql);
		foreach ($blocks as $k => $v) {
			$blocks[$k]['raw'] = unserialize($v['raw']);
		}
		return $blocks;
	}
	public function getTransactionsOut($txid) {
		$mysql = new Mysql();
		$sql = "SELECT  *  FROM transactions tr JOIN transactions_out tro ON tr.`txid` = tro.`txid`  WHERE tr.`txid` = " . $mysql->escape($txid);
		$transactions = $mysql->select($sql);


		return $transactions;
	}

	public function getTransaction($txId) {
		$mysql = new Mysql();
		$block = $mysql->select("SELECT * FROM transactions t WHERE `txid` = " . $mysql->escape($txId));
		return $block[0];
	}


} 