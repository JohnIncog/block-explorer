<?php
$richList = $this->getData('richList');
$distribution = $this->getData('distribution');
?>
<div class="my-template">

	<?php $this->render('page_header'); ?>

	<?php $this->render('market_info'); ?>

	<ul class="nav nav-tabs">
		<li role="presentation" ><a href="/">Latest Blocks</a></li>
		<li role="presentation"><a href="/latesttransactions">Latest Transactions</a></li>
		<li role="presentation" class="active"><a href="/richlist">Rich List</a></li>
		<li role="presentation"><a href="/primestakes">Prime Stakes</a></li>
	</ul>

	<div class="row">
		<div class="col-md-7" style="padding-right: 0">
		<table class="table infoTable table-invert table-hover">
			<thead>
			<tr>
				<th class="text-right" style="width: 45px">Rank</th>
				<th>Address</th>
				<th>Balance</th>
				<th>Percent of coins</th>
			</tr>
			</thead>

			<?php foreach($richList as $rich) { ?>
				<tr>
					<td class="text-right"><?php echo $rich['rank'] ?></td>
					<td><?php echo \lib\Helper::getAddressLink($rich['address']) ?></td>
					<td><?php echo \lib\Helper::formatXPY($rich['balance']) ?></td>
					<td><?php echo $rich['percent'] ?> %</td>
				</tr>
			<?php } ?>
			</table>

		</div>
		<div class="col-md-5" style="padding-left: 0">
			<table class="table infoTable table-invert table-hover" style="margin-bottom: 0;">
				<thead>
				<tr>
					<th>Top</th>
					<th>Holdings</th>
					<th>Percent</th>
				</tr>
				</thead>
				<?php
				$last = end($distribution);
				?>
				<?php foreach($distribution as &$row) {
					$row['percent'] = round($row['holdings']/$last['holdings']*100, 2);
					?>
					<tr>
						<td><?php echo $row['top'] ?></td>
						<td><?php echo \lib\Helper::formatXPY($row['holdings']) ?></td>
						<td><?php echo $row['percent'] ?> %</td>
					</tr>
				<?php }?>
			</table>


			<div id="container" style="min-width: 310px; height: 400px; max-width: 600px; margin: 0 auto"></div>

		</div>
	</div>
</div>

<script type="text/javascript">
	$(function () {
		$('#container').highcharts({
			chart: {
				plotBackgroundColor: null,
				plotBorderWidth: null,
				plotShadow: false,
				backgroundColor: 'rgba(0, 0, 0, 0.2)'
			},
			title: {
				text: 'Distribution',
				style: {
					color: 'white'
				}
			},
			tooltip: {
				pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
			},
			plotOptions: {
				pie: {
					allowPointSelect: true,
					cursor: 'pointer',
					dataLabels: {
						enabled: true,
						format: '<b>{point.name}</b>: {point.percentage:.1f} %',
						style: {
							color: 'white'
						}
					}
				}
			},
			series: [{
				type: 'pie',
				name: 'Distribution',
				data: [
					['Top 10',       43],
					['Top 100',       47],
					['Top 1000',    8],
					['Other',    2]

				]
			}]
		});
	});

	$.ajax({
		url: "/api/latestblocks?limit=1",
		context: document.body
	}).done(function(data) {

			console.log(data)
			blockHeight = data.data[0].height;
			$("#outstanding").text(addCommas((data.data[0]['outstanding']*1).toString()) + ' XPY');


		});

	(function poll() {
		setTimeout(function() {
			$.ajax({
				url: "/api/latestblocks",
				type: "GET",
				data: { height: blockHeight },
				success: function(data) {
					console.log("polling");
//						console.log(data);
					blockHeight = data.data[0].height; // Store Blockheight
					$("#outstanding").text(addCommas((data.data[0]['outstanding']*1).toString()) + ' XPY');

				},
				dataType: "json",
				complete: poll,
				timeout: 2000
			})
		}, 55000);
	})();


</script>


<script src="/highcharts/js/highcharts.js"></script>
<script src="/highcharts/js/modules/exporting.js"></script>
