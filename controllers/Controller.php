<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 5/15/15
 * Time: 10:59 PM
 */

namespace controllers;


class Controller {

	public $data;

	/** @var \PP\Bootstrap */
	public $bootstrap;

	public function __construct($bootstrap) {
		$this->bootstrap = $bootstrap;
	}

	public function setData($key, $value) {
		$this->data[$key] = $value;
	}

	public function getData($key, $default = false) {
		$return = $default;
		if (isset($this->data[$key])) {
			$return = $this->data[$key];
		}
		return $return;
	}

	public function render($view, $ext='.php') {

		include('../views/' . $view . $ext);

	}

} 