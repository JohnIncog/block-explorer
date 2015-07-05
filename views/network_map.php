<?php
$network = $this->getData('network');
$networkData = $this->getData('networkData');
$limit = $this->getData('limit');
//var_dump($network['totalConnections']);
$json = '';
foreach ($networkData as $country => $connections) {
	$json .= json_encode(array('hc-key' => $country, 'value' => (int)$connections)) . ",\n";
}
$json = substr($json, 0, -2);
?>


<div class="my-template">

	<?php $this->render('page_header'); ?>

	<div class="alert alert-success alert-dismissible" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<p>Network data is gathered from one of the DNS Seed servers and is updated every 15 minutes.</p>
		<p>If you have an active node with a static IP and
		would like to provide data, Please <a style="color: blue" href="/contact">contact us</a>.</p>
	</div>

	<?php $this->render('market_info'); ?>

	<?php $this->render('tabs'); ?>

<script>
	var cdata = [ <?php echo $json; ?>];
</script>

	<div class="row">
		<div class="col-md-5" style="margin-right: 0px; padding-right: 0px;">
			<table class="table infoTable table-invert">
				<thead>
				<tr>
					<th>Connections</th>
					<th>Country</th>
					<th>State</th>
					<th>City</th>
				</tr>
				</thead>
				<?php foreach($network as $row) {  ?>
					<tr>
						<td><?php echo $row['connections'] ?></td>
						<td><?php echo $row['country_code'] ?></td>
						<td><?php echo $row['state'] ?></td>
						<td><?php echo $row['city'] ?></td>
					</tr>
				<?php } ?>

			</table>

		</div>
		<div class="col-md-7" style="margin-left: 0px; padding-left: 0px;"><div id="container2"></div></div>
	</div>



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


</div>


