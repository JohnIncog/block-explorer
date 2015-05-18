<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 5/15/15
 * Time: 10:17 PM
 */

namespace controllers;
use PP\PaycoinDb;

class Home extends Controller {

	public function index() {

		echo 'index';

	}

	public function pageNotFound() {
		echo 'page not found';
	}

	public function getTransaction() {

	}

	public function test() {



		echo "<pre>\n";
		$paycoin = new Paycoin();
		print_r($paycoin->getLastBlocks());
		print_r($paycoin->getInfo());
		print_r($paycoin->help());
		return;
		echo "</pre>";
	}

} 