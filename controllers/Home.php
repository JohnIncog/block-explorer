<?php

namespace controllers;

class Home extends Controller {

	public function index() {
	}

	public function pageNotFound() {

		$this->setData('pageTitle', 'Search');
		$this->render('header');
		$this->render('404');
		$this->render('footer');

	}

} 