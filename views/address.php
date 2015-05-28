<?php
$address = $this->getData('address');
$limit = $this->getData('limit');
$addressInformation = $this->getData('addressInformation');
?>


<div class="my-template">

	<?php $this->render('page_header'); ?>


	<h1 class="text-left">Details for Address</h1>

	<?php if (count($addressInformation['transactions']) == 0)  { ?>
		<div class="infoTable">

			<div class="alert alert-warning" role="alert">
				<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
				<span class="sr-only"></span>
				No transactions found for this address
			</div>


		</div>
	<?php } else { ?>

	<table class="table infoTable">
		<tr>
			<td>Address</td><td><?php echo \PP\Helper::getAddressLink($address); ?></td>
			<td rowspan="5" class="text-right col-sm-2"><div id="qrcode"></div></td>
		</tr>
		<tr>
			<td>Balance</td>
			<td><strong><?php echo \PP\Helper::formatXPY($addressInformation['balance']); ?></strong></td>
		</tr>
		<?php if ($addressInformation['rank'] > 0) { ?>
		<tr>
			<td>Rich List</td>
			<td>Rank <?php echo $addressInformation['rank']; ?></td>
		</tr>
		<?php } ?>
		<tr>
			<td>Received</td>
			<td><?php echo \PP\Helper::formatXPY($addressInformation['totalInValue']); ?> in <?php echo $addressInformation['totalInTransactions'] ?> transactions</td>
		</tr>
		<?php if (isset($addressInformation['totalOutTransactions'])) { ?>
		<tr>
			<td>Sent</td>
			<td><?php echo \PP\Helper::formatXPY($addressInformation['totalOutValue']); ?> in <?php echo $addressInformation['totalOutTransactions'] ?> transactions</td>
		</tr>
		<?php } ?>
		<?php if (isset($addressInformation['totalStakeTransactions'])) {  ?>
		<tr>
			<td>Staked</td>
			<td><?php echo \PP\Helper::formatXPY($addressInformation['totalStakeValue']); ?> in <?php echo $addressInformation['totalStakeTransactions'] ?> transactions</td>
		</tr>
		<?php } ?>
	</table>

		<div class="row">
			<div class="col-md-6"><h2 class="text-left">Transactions</h2></div>
			<div class="col-md-2"></div>
			<div class="col-md-4 pull-right">
				<form method="post">
					<div class="form-group col-sm-8 ">
						<select name="limit" class="form-control">
							<option value="100">Show 100 Transactions</option>
							<option value="1000">Show 1000 Transactions</option>
							<option value="all">Show All Transactions</option>
						</select>
					</div>
					<div class="col-md-2 form-group">
						<input type="submit" value="Go" class="btn btn-default">
					</div>
				</form>

			</div>

		</div>

	<table class="table infoTable" id="transactionTable">
		<thead>
		<tr>
			<th>Hash</th>
			<th data-sort="int">Block</th>
			<th data-sort="string">Date/Time</th>
			<th >Amount</th>
			<th class="text-right">Balance</th>
		</tr>
		</thead>
		<tbody>
		<?php foreach ($addressInformation['transactions'] as $i => $t) { ?>
		<tr>
			<td><?php echo \PP\Helper::getTxHashLink($t['txid']) ?></td>
			<td><?php echo $t['block_height'] ?></td>
			<td><?php echo \PP\Helper::formatTime($t['time']) ?></td>
			<td>
				<?php
				if ($t['value'] > 0) {
					echo '<span class="addressReceive">+';
				} else {
					echo '<span class="addressSend">';
				}
				?>
				<?php echo \PP\Helper::formatXPY($t['value']);
				echo '</span>';
				if ($t['type'] == 'stake') {
					echo '<span class="label label-info" style="margin-left: 15px">Stake</span>';
				}

				?>
			</td>
			<td class="text-right"><?php echo \PP\Helper::formatXPY($t['balance']) ?></td>
		</tr>
		<?php } ?>
		</tbody>
	</table>

	<div>
		<?php
		if ($addressInformation['totalTransactions'] > $limit) {
			if ($limit != 'all') {
				$showing = $limit . ' of';
			}
			echo "Showing {$showing} {$addressInformation['totalTransactions']} Transactions";
		}


		?>
	</div>


	<?php }  ?>

</div>



<script src="/js/jquery.qrcode-0.12.0.min.js"></script>

<script>
	var address = <?php echo json_encode($addressInformation['address']); ?>;
	$("#transactionTable").stupidtable();

	$('#qrcode').qrcode({
		size: 175,

		"color": "#3a3",
		"text": address

	});

</script>