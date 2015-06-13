<?php
$chart = $this->getData('chart', 'outstanding');
$blocks = $this->getData('blocks');
$categories = $this->getData('categories');
$dataPoints = $this->getData('dataPoints');
$limit = $this->getData('limit');

?>
<div class="my-template">

	<?php $this->render('page_header'); ?>

	<?php $this->render('market_info'); ?>

	<?php $this->render('tabs'); ?>


	<div id="container" style="height: 400px; min-width: 310px">
		<div style="padding: 30px"></div>
		<div class="atebits-loader">
			Loading…
		</div>
		<div style="padding: 30px"></div>
		<div>Loading Graph...</div>
	</div>


	<script>

		window.onload = function () {

			$(function () {
				$('#container').highcharts({
					chart: {
						type: 'spline',
						zoomType: 'x'
					},
					title: {
						text: 'Transactions Per Block',
						x: -20 //center
					},
					subtitle: {
						text: 'Click and drag in the plot area to zoom in',
						x: -20
					},
					xAxis: {
						categories: <?php echo $categories ?>
					},
					yAxis: {
						title: {
							text: 'Transactions'
						},
						plotLines: [{
							value: 0,
							width: 1,
							color: '#808080'
						}]
					},
//					tooltip: {
//						valueSuffix: ''
//					},
//					legend: {
//						layout: 'vertical',
//						align: 'right',
//						verticalAlign: 'middle',
//						borderWidth: 0
//					},
					tooltip: {
						headerFormat: '<b>Block: </b>{point.x}<br>'
						//pointFormat: '{point.x:%e. %b}: {point.y:.2f} m'
					},

					series: [{
						name: 'Transactions',
						data: <?php echo $dataPoints ?>
					}
//					}, {
//						name: 'New York',
//						data: [-0.2, 0.8, 5.7, 11.3, 17.0, 22.0, 24.8, 24.1, 20.1, 14.1, 8.6, 2.5]
//					}, {
//						name: 'Berlin',
//						data: [-0.9, 0.6, 3.5, 8.4, 13.5, 17.0, 18.6, 17.9, 14.3, 9.0, 3.9, 1.0]
//					}, {
//						name: 'London',
//						data: [3.9, 4.2, 5.7, 8.5, 11.9, 15.2, 17.0, 16.6, 14.2, 10.3, 6.6, 4.8]
//					}
					]
				});
			});

		};
	</script>


<!--	<li><a href="?type=line-chart">line</a></li>-->
<!--	<li><a href="?type=area">area</a></li>-->
<!--	<li><a href="?type=spline">spline</a></li>-->




</div>