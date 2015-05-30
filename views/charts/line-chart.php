<?php
$chart = $this->getData('chart', 'outstanding');
?>
<div class="my-template">

	<?php $this->render('page_header'); ?>

	<h1><?php echo ucfirst($chart); ?></h1>
	<script>
		$(function () {

			$.getJSON('/api/charts/<?php echo $chart; ?>?callback=?', function (data) {
				// Create the chart
				$('#container').highcharts('StockChart', {
					chart: {
						zoomType: 'x'
					},

					rangeSelector: {
						buttons : [{
							type : 'day',
							count : 1,
							text : '1d'
						}, {
							type : 'week',
							count : 1,
							text : '1w'
						}, {
							type : 'month',
							count : 1,
							text : '1m'
						}, {
							type : 'ytd',
							text : 'YTD'
						}, {
							type: 'year',
							count: 1,
							text: '1y'
						}, {
							type : 'all',
							count : 1,
							text : 'All'
						}],
						selected: 1
					},

					title: {
						text: '<?php echo ucfirst($chart); ?>	'
					},

					series: [
						{
							name: 'XPY',
							data: data,
							threshold : null,
							type : 'line',
							tooltip: {
								valueDecimals: 6
							}



						}
					]
				});
			});

		});

	</script>
	</head>
	<body>
	<script src="/highstock/js/highstock.js"></script>
	<script src="/highstock/js/modules/exporting.js"></script>

	<li><a href="?type=line-chart">line</a></li>
	<li><a href="?type=area">area</a></li>
	<li><a href="?type=spline">spline</a></li>

	<div id="container" style="height: 400px; min-width: 310px">
		<div style="padding: 30px"></div>
		<div class="atebits-loader">
			Loading…
		</div>
		<div style="padding: 30px"></div>
		<div>Loading Graph...</div>
	</div>


</div>