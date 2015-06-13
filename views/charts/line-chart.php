<?php
$chart = $this->getData('chart', 'outstanding');
?>
<div class="my-template">

	<?php $this->render('page_header'); ?>

	<h1><?php echo ucfirst($chart); ?></h1>
	<script>
		var chart = <?php echo json_encode($chart); ?>;
		var chartdata = '/api/charts/'+chart+'?callback=?';
	</script>

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