<?php
/**
 * @author John <john@paycoin.com>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */
namespace controllers;

use lib\Bootstrap;

/**
 * Class Home
 * @package controllers
 */
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
				if (DEBUG_BAR) {
					Bootstrap::getInstance()->debugbar['messages']->error("Fatal error: $errstr on line $errline in file $errfile");
				}
				include('../views/header.php');
				include('../views/error.php');
				include('../views/footer.php');

				exit(1);
				break;

			case E_USER_WARNING:
				if (DEBUG_BAR) {
					Bootstrap::getInstance()->debugbar['messages']->warning("Warning: $errstr on line $errline in file $errfile");
				}
				break;

			case E_USER_NOTICE:
				if (DEBUG_BAR) {
					Bootstrap::getInstance()->debugbar['messages']->notice("Notice: $errstr on line $errline in file $errfile");
				}
				break;

			case E_USER_DEPRECATED:
				if (DEBUG_BAR) {
					Bootstrap::getInstance()->debugbar['messages']->warning("Depricated: $errstr on line $errline in file $errfile");
				}
				break;

			default:
				if (DEBUG_BAR) {
					Bootstrap::getInstance()->debugbar['messages']->addMessage("Unknown Error: $errstr on line $errline in file $errfile");
				}
				break;
		}

		//return false;
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