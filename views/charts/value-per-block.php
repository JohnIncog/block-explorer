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


	<script>


		window.onload = function () {

			$(function () {
				$('#container').highcharts({
					chart: {
						type: 'area',
						zoomType: 'x'
					},
					title: {
						text: 'Value Per Block',
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
							text: 'Value'
						},
						plotLines: [{
							value: 0,
							width: 1,
							color: '#808080'
						}]
					},


//					legend: {
//						layout: 'vertical',
//						align: 'right',
//						verticalAlign: 'middle',
//						borderWidth: 0
//					},
					tooltip: {
						headerFormat: '<b>Block: </b>{point.x}<br>',
						valueSuffix: ' XPY'

						//pointFormat: '{point.x:%e. %b}: {point.y:.2f} m'
					},
					plotOptions: {
						area: {
							stacking: 'normal',
							lineColor: '#666666',
							lineWidth: 1,
							marker: {
								lineWidth: 1,
								lineColor: '#666666'
							}
						}
					},
					series: [{
						name: 'Value Out',
						data: <?php echo $dataPoints[0] ?>
					}, {
						name: 'Value In',
						data: <?php echo $dataPoints[1] ?>
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

	<div id="container" style="height: 400px; min-width: 310px">
		<div style="padding: 30px"></div>
		<div class="atebits-loader">
			Loading…
		</div>
		<div style="padding: 30px"></div>
		<div>Loading Graph...</div>
	</div>


</div>