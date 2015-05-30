<?php

namespace controllers;

use PP\PaycoinDb;

class Chart extends Controller {

	public function index() {

	}

	public function chart() {

		$chartType = $this->bootstrap->httpRequest->get('type', 'line-chart');
		$chart = $this->bootstrap->route['chart'];
		if (!$chart) {
			$chart = 'outstanding';
		}
		$validChartTypes = array('line-chart', 'area', 'spline');
		if (!in_array($chartType, $validChartTypes)) {
			$chartType = 'line-chart';
		}

		$this->setData('pageTitle', 'Charts');
		$this->setData('chart', $chart);
		$this->render('header');
		$this->render('charts/' . $chartType);
		$this->render('footer');
	}

	public function getChartData() {

		header("Content-Type: text/javascript");
		$paycoinDb = new PaycoinDb();

		$chart =  $this->bootstrap->route['chart'];
		if (!$chart) {
			$chart = 'outstanding';
		}

		switch ($chart) {
			case 'difficulty':
				$dataPoints = $paycoinDb->getDifficultyDataPoints(100000);
				break;
			default:
				$dataPoints = $paycoinDb->getOutstandingDataPoints(100000);

		}


		echo $this->bootstrap->httpRequest->get('callback') . "( [ \n";
		echo join($dataPoints, ",  \n");
		echo "\n ]);\n";
	}

}