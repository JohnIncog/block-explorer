<?php
$address = $this->getData('address');
$limit = $this->getData('limit');
$addressInformation = $this->getData('addressInformation');
?>


<div class="my-template">

	<?php $this->render('page_header'); ?>


	<h2 class="text-left">Details for Address</h2>

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
			<td class="col-md-2">Address</td>
			<td>
				<form id="tag-address-form">
					<input name="address" type="hidden" value="<?php echo htmlspecialchars($address); ?>">
				<div class="row">
					<div class="col-md-5"><?php echo htmlspecialchars($address); ?></div>
					<?php if (!empty($addressInformation['addressTag']['tag'])) {
						if ($addressInformation['addressTag']['verified'] == 0) { ?>
							<div class="col-md-2">
								<h4 style="margin-top: 0; margin-bottom: 0;">
									<span class="label label-primary tagged-tag"><?php echo htmlspecialchars($addressInformation['addressTag']['tag']) ?></span>
								</h4>
							</div>
							<div class="col-md-5 text-right">
								<button type="button" class="btn btn-danger btn-xs" id="remove-tag">Remove Tag</button>
							</div>
						<?php } elseif ($addressInformation['addressTag']['verified'] == 3) { ?>
							<div class="col-md-2">
								<h4 style="margin-top: 0; margin-bottom: 0;">
									<span class="label label-danger tagged-tag" style="text-decoration: line-through"><?php echo htmlspecialchars($addressInformation['addressTag']['tag']) ?></span>
								</h4>
							</div>
							<div class="col-md-5 text-right">
								<button type="button" class="btn btn-primary btn-xs" id="claim-address">Claim Address</button>
							</div>
						<?php } elseif ($addressInformation['addressTag']['verified'] == 1) { ?>
							<div class="col-md-7">
								<h4 style="margin-left: 5px; margin-top: 0; margin-bottom: 0;">
									<?php if (empty($addressInformation['addressTag']['url'])) { ?>
										<span class="label label-success tagged-tag">
										<?php echo htmlspecialchars($addressInformation['addressTag']['tag']) ?>

									<?php } else { ?>
											&nbsp;&nbsp;
											<a class="label label-success tagged-tag" target="_blank" href="<?php echo htmlspecialchars($addressInformation['addressTag']['url']) ?>"
													   title="<?php echo htmlspecialchars($addressInformation['addressTag']['url']) ?>">
											<?php echo htmlspecialchars($addressInformation['addressTag']['tag']) ?>
												&nbsp;&nbsp;
											<span class="glyphicon glyphicon-new-window" aria-hidden="true"></span><a>

										<?php } ?>
									</span>
								</h4>
							</div>
						<?php } ?>



					<?php } else { ?>
						<div class="col-md-4">

							<input name="tag" type="text" class="form-control pull-left" id="tagAddress" placeholder="Add a Tag to Address">
						</div>
						<div class="col-md-3">
							<button type="submit" class=" pull-left btn btn-default">Submit</button>
						</div>
					<?php }  ?>

				</div>
				</form>
			</td>
			<td rowspan="100%" class="text-right col-sm-2" style="padding: 15px;">
				<div id="qrcode"></div></td>
		</tr>
		<tr>
			<td>Balance</td>
			<td><strong><?php echo \lib\Helper::formatXPY($addressInformation['balance']); ?></strong></td>
		</tr>
		<?php if ($addressInformation['rank'] > 0) { ?>
		<tr>
			<td>Rich List</td>
			<td>Rank <?php echo $addressInformation['rank']; ?></td>
		</tr>
		<?php } ?>
		<?php if (isset($addressInformation['totalInTransactions'])) { ?>
		<tr>
			<td>Received</td>
			<td><?php echo \lib\Helper::formatXPY($addressInformation['totalInValue']); ?> in <?php echo $addressInformation['totalInTransactions'] ?> transactions</td>
		</tr>
		<?php } ?>
		<?php if (isset($addressInformation['totalOutTransactions'])) { ?>
		<tr>
			<td>Sent</td>
			<td><?php echo \lib\Helper::formatXPY($addressInformation['totalOutValue']); ?> in <?php echo $addressInformation['totalOutTransactions'] ?> transactions</td>
		</tr>
		<?php } ?>
		<?php if (isset($addressInformation['totalStakeTransactions'])) {  ?>
		<tr>
			<td>Staked</td>
			<td><?php echo \lib\Helper::formatXPY($addressInformation['totalStakeValue']); ?> in <?php echo $addressInformation['totalStakeTransactions'] ?> transactions</td>
		</tr>
		<?php } ?>
		<?php if (isset($addressInformation['totalCreationTransactions'])) {  ?>
			<tr>
				<td>Mined</td>
				<td><?php echo \lib\Helper::formatXPY($addressInformation['totalCreationValue']); ?> in <?php echo $addressInformation['totalCreationTransactions'] ?> transactions</td>
			</tr>
		<?php } ?>
		<?php if (isset($addressInformation['totalTransactions'])) {  ?>
			<tr>
				<td>Total Transactions</td>
				<td><?php echo $addressInformation['totalTransactions']?></td>
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

	<table class="table latestTransactions" id="transactionTable">
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
			<td><?php echo \lib\Helper::getTxHashLink($t['txid']) ?></td>
			<td><?php echo $t['block_height'] ?></td>
			<td><?php echo \lib\Helper::formatTime($t['time']) ?></td>
			<td>
				<?php
				if ($t['value'] > 0) {
					echo '<span class="addressReceive">+';
				} else {
					echo '<span class="addressSend">';
				}
				?>
				<?php echo \lib\Helper::formatXPY($t['value']);
				echo '</span>';
				if ($t['type'] == 'stake') {
					echo '<span class="label label-info" style="margin-left: 15px">Stake</span>';
				}

				?>
			</td>
			<td class="text-right"><?php echo \lib\Helper::formatXPY($t['balance']) ?></td>
		</tr>
		<?php } ?>
		</tbody>
	</table>

	<div>
		<?php
		if ($addressInformation['totalTransactions'] > $limit) {
			$showing = 'all';
			if ($limit != 'all') {
				$showing = $limit . ' of';
			}
			echo "Showing {$showing} {$addressInformation['totalTransactions']} Transactions";
		}


		?>
	</div>


	<?php }  ?>

</div>

<?php if ($addressInformation['totalTransactions'] > 25) { ?>
<button id="toTop" type="button" class="btn btn-default btn-lg to-top pull-right">
	<span class="glyphicon glyphicon-chevron-up" aria-hidden="true"></span> Top
</button>
<?php } ?>


<script>
	var address = <?php echo json_encode($addressInformation['address']); ?>;
</script>