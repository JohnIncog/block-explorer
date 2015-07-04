<?php
$network = $this->getData('network');
$limit = $this->getData('limit');
//var_dump($network['totalConnections'])
?>


<div class="my-template">

	<?php $this->render('page_header'); ?>

	<?php $this->render('market_info'); ?>

	<?php $this->render('tabs'); ?>

	<div class="row">
		<div class="col-md-8" style="padding-right: 0">
		<table class="table latestTransactions table-invert table-hover">
			<thead>
			<tr>
				<th>Sub-version</th>
				<th></th>
				<th>Protocol</th>
				<th>Count</th>
				<th>Network Share</th>
			</tr>
			</thead>
			<?php foreach($network['subVersions'] as $q) { ?>
				<tr>
					<td><?php echo $q['subver'] ?></td>
					<td><!-- <?php echo $q['subver'] ?> -->
<!--						<a href="#" class="node-list" id="--><?php //echo $q['subver'] ?><!--"><span class="label label-default">node list</span></a>-->
						<button type="button" class="btn btn-primary btn-xs node-list" data-toggle="modal" data-target="#myModal" id="<?php echo $q['subver'] ?>">
							node list
						</button>
					</td>
					<td><?php echo $q['version'] ?></td>
					<td><?php echo $q['connections'] ?></td>
					<td><?php echo $q['share'] ?> %</td>
				</tr>
			<?php } ?>
		</table>
		</div>
		<div class="col-md-4" style="padding-left: 0">
			<table class="table infoTable table-invert table-hover" style="margin-bottom: 0;">
				<div id="container" style="min-width: 310px; height: 400px; max-width: 600px; margin: 0 auto"></div>
			</table>
		</div>
	</div>


	<!-- Button trigger modal -->


	<!-- Modal -->
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="node-modal-title">Modal title</h4>
				</div>
				<div class="modal-body" id="node-modal">
					Seen in the last 24 hours
					<textarea class="form-control" readonly="" style="cursor:text" rows="10" id="node-list-nodes"></textarea>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>

	<script>
		var options = {
			chart: {
				type: 'pie',
				options3d: {
					enabled: true,
					alpha: 45
				}
			},
			title: {
				text: 'Sub-Versions',
				style: {
					color: 'white'
				}
			},
			tooltip: {
				pointFormat: '<b>{point.percentage:.1f}%</b>'
			},
			plotOptions: {
				pie: {

					innerSize: 100,
					depth: 45,

					allowPointSelect: true,
					cursor: 'pointer',
					dataLabels: {
						enabled: false,
						format: '<b>{point.name}</b>: {point.percentage:.1f} %',
						showInLegend: true

					}
				}
			},
			series: [{
				name: 'Sub-Version',
				data: [ <?php foreach ($network['graphData'] as $subver => $connections) {
					echo "[\"$subver\", $connections], \n";
				}
				?> ]
			}]
		};
	</script>



	<style>#container2 {
			height: 500px;
			min-width: 310px;
			max-width: 800px;
			margin: 0 auto;
		}
		.loading {
			margin-top: 10em;
			text-align: center;
			color: gray;
		}</style>
	<div id="container2"></div>

</div>


