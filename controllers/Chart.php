<?php

namespace controllers;

use lib\PaycoinDb;
use Symfony\Component\Console\Helper\InputAwareHelper;

class Chart extends Controller {

	public function index() {

	}

	private function getLimit($default = 100, $max = 10000) {
		$limit = $this->bootstrap->httpRequest->get('limit');
		if (!$limit) {
			$limit = $default;
		}
		if ($limit > $max) {
			$limit = $max;
		}
		$this->setData('limit', $limit);
		return $limit;
	}

	public function valuePerBlock() {

		$this->setData('activeTab', 'Charts');
		$this->setData('activePulldown', 'Transactions Per Block');

		$this->addJs('/js/market_info.js');
		$this->addJs('/js/update_outstanding.js');

		$this->addJs('/highstock/js/highstock.js');
		$this->addJs('/highstock/js/modules/exporting.js');
		$this->addJs('/js/charts/theme.js');

		$this->setData('pageTitle', 'Charts - Value Per Block');

		$limit = $this->getLimit(25);

		$options = array(
			array('value' => '25', 'name' => 'Last 25 Blocks'),
			array('value' => '100', 'name' => 'Last 100 Blocks'),
			array('value' => '1000', 'name' => 'Last 1000 Blocks'),
			array('value' => '2500', 'name' => 'Last 2500 Blocks'),
		);
		$limitSelector = '<li class="pull-right"><form method="post">
		<div class="form-group col-sm-10" style="margin-bottom: 0px">
		<select name="limit" class="form-control">';
		foreach ($options as $option) {
			$limitSelector .='<option value="' . $option['value'] . '" ';
			if ($this->getData('limit') == $option['value']) {
				$limitSelector .= 'selected';
			}
			$limitSelector .= '>' . $option['name'] . '</option>';
		}
		$limitSelector .= '</select></div>
			<div class="col-sm-2 form-group" style="margin-bottom: 0px">
				<input type="submit" value="Go" class="btn btn-default">
			</div>
		</form>
		</li>';
		$this->setData('limitSelector', $limitSelector);

		$paycoinDb = new PaycoinDb();
		$blocks = $paycoinDb->getLatestBlocks($limit, 0, 600);

		$blocks = array_reverse($blocks);
		$blocks = array_column($blocks, null, 'height');
		$categories = json_encode(array_keys($blocks));

		$dataPoints[0] = array_column($blocks, 'valueout');
		array_walk($dataPoints[0], function(&$val) {
			$val = (float)$val;
		});
		$dataPoints[0] = json_encode($dataPoints[0]);

		$dataPoints[1] = array_column($blocks, 'valuein');
		array_walk($dataPoints[1], function(&$val) {
			$val = (float)$val;
		});
		$dataPoints[1] = json_encode($dataPoints[1]);

		$this->setData('categories', $categories);
		$this->setData('dataPoints', $dataPoints);

		$this->render('header');
		$this->render('charts/value-per-block');
		$this->render('footer');
	}


	public function transactionsPerBlock() {


		$this->setData('activeTab', 'Charts');
		$this->setData('activePulldown', 'Transactions Per Block');

		$this->addJs('/js/market_info.js');
		$this->addJs('/js/update_outstanding.js');

		$this->addJs('/highstock/js/highstock.js');
		$this->addJs('/highstock/js/modules/exporting.js');
		$this->addJs('/js/charts/theme.js');

		$this->setData('pageTitle', 'Charts - Value Per Transaction');
		$limit = $this->getLimit(50);

		$options = array(
			array('value' => '100', 'name' => 'Last 100 Blocks'),
			array('value' => '1000', 'name' => 'Last 1000 Blocks'),
			array('value' => '2500', 'name' => 'Last 2500 Blocks'),
		);
		$limitSelector = '<li class="pull-right"><form method="post">
			<div class="form-group col-sm-10" style="margin-bottom: 0px">
			<select name="limit" class="form-control">';
		foreach ($options as $option) {
			$limitSelector .='<option value="' . $option['value'] . '" ';
			if ($this->getData('limit') == $option['value']) {
				$limitSelector .= 'selected';
			}
			$limitSelector .= '>' . $option['name'] . '</option>';
		}
		$limitSelector .= '</select></div>
				<div class="col-sm-2 form-group" style="margin-bottom: 0px">
					<input type="submit" value="Go" class="btn btn-default">
				</div>
			</form>
			</li>';
		$this->setData('limitSelector', $limitSelector);


		$paycoinDb = new PaycoinDb();
		$blocks = $paycoinDb->getLatestBlocks($limit);

		$blocks = array_reverse($blocks);
		$blocks = array_column($blocks, null, 'height');
		$categories = json_encode(array_keys($blocks));
		$dataPoints = array_column($blocks, 'transactions');
		array_walk($dataPoints, function(&$val) {
			$val = (int)$val;
		});
		$dataPoints = json_encode($dataPoints);
		$this->setData('categories', $categories);
		$this->setData('dataPoints', $dataPoints);

		$this->render('header');
		$this->render('charts/transaction-per-block');
		$this->render('footer');
	}

	public function chart() {

		$this->addJs('/js/charts/theme.js');
		$this->addJs('/highstock/js/highstock.js');
		$this->addJs('/highstock/js/modules/exporting.js');


		$chartType = $this->bootstrap->httpRequest->get('type', 'area');
		$chart = $this->bootstrap->route['chart'];
		if (!$chart) {
			$chart = 'outstanding';
		}

		$validChartTypes = array('line', 'area', 'spline');
		if (!in_array($chartType, $validChartTypes)) {
			$chartType = 'area';
		}

		$this->addJs('/js/charts/' . $chartType . '.js');

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

			case 'transactions-per-block':
				$dataPoints = $paycoinDb->getTransactionsPerBlockDataPoints(100000);
				break;
			case 'difficulty':
				$dataPoints = $paycoinDb->getDifficultyDataPoints(100000);
				break;
			default:
				$dataPoints = $paycoinDb->getOutstandingDataPoints(100000000);

		}


		echo $this->bootstrap->httpRequest->get('callback') . "( [ \n";
		echo join($dataPoints, ",");
		echo "\n ]);\n";
	}

}