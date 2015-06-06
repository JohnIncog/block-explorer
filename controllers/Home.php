<?php

namespace controllers;

class Home extends Controller {

	public function index() {
	}

	public static function myErrorHandler($errno, $errstr, $errfile, $errline) {
		if (!(error_reporting() & $errno)) {
			// This error code is not included in error_reporting
			return;
		}

		switch ($errno) {
			case E_USER_ERROR:
//				echo "<b>My ERROR</b> [$errno] $errstr<br />\n";
//				echo "  Fatal error on line $errline in file $errfile";
//				echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
//				echo "Aborting...<br />\n";
				error_log("Fatal error: $errstr on line $errline in file $errfile");
				include('../views/header.php');
				include('../views/error.php');
				include('../views/footer.php');

				exit(1);
				break;

//			case E_USER_WARNING:
//				echo "<b>My WARNING</b> [$errno] $errstr<br />\n";
//				break;
//
//			case E_USER_NOTICE:
//				echo "<b>My NOTICE</b> [$errno] $errstr<br />\n";
//				break;
//
//			default:
//				echo "Unknown error type: [$errno] $errstr<br />\n";
//				break;
		}

		return false;
		/* Don't execute PHP internal error handler */
		return true;
	}

	public function pageNotFound() {

		$this->setData('pageTitle', 'Search');
		$this->render('header');
		$this->render('404');
		$this->render('footer');

	}

} 