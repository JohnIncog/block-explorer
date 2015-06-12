<?php

namespace controllers;


class Controller {

	public $data;

	/** @var \lib\Bootstrap */
	public $bootstrap;

	public $assets;

	public function __construct($bootstrap) {
		$this->bootstrap = $bootstrap;
	}

	public function getConfig($config, $default = false) {
		return $this->bootstrap->getConfig($config, $default);
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

	public function addJs($path) {
		$this->assets['js'][] = $path;
	}

	public function addCss($path) {
		$this->assets['css'][] = $path;
	}

	public function getHeaderAssets() {
		$html = '';
		if (isset($this->assets['css'])) {
			foreach ($this->assets['css'] as $css) {
				$html .= '	<link href="' . $css . '" rel="stylesheet">';
			}
		}
		return $html;
	}

	public function getJsAssets() {
		$html = '';
		if (isset($this->assets['js'])) {
			foreach ($this->assets['js'] as $js) {
				$html .= '<script type="application/javascript" src="' . $js . '?cb=' . APP_VERSION . '"></script>';
			}
		}
		return $html;
	}

	public function render($view, $ext='.php') {

		$viewPath = __DIR__ . '/../views/' . $view . $ext;
		include($viewPath);

	}

}