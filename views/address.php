<?php
$address = $this->getData('address');
$addressInformation = $this->getData('addressInformation');
?>


<div class="my-template">

	<?php $this->render('page_header'); ?>


	<h1>Wallet Details</h1>

	<?php if (count($addressInformation['transactions']) == 0)  { ?>
		<div class="blockTable">

			<div class="alert alert-warning" role="alert">
				<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
				<span class="sr-only"></span>
				No transactions found for this address
			</div>


		</div>
	<?php } else { ?>

	<table class="table blockTable">
		<tr>
			<td>Address</td><td><?php echo \PP\Helper::getAddressLink($address); ?></td>
		</tr>
		<tr>
			<td>Balance</td>
			<td><?php echo \PP\Helper::formatXPY($addressInformation['balance']); ?></td>
		</tr>
		<?php if ($addressInformation['rank'] > 0) { ?>
		<tr>
			<td>Rank</td>
			<td><?php echo $addressInformation['rank']; ?></td>
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

	<table class="table blockTable">
		<tr>
			<th>Hash</th>
			<th>Block</th>
			<th>Date/Time</th>
			<th>Amount</th>
			<th>Balance</th>
		</tr>
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
					echo ' <span class="label label-info">Stake</span>';
				}

				?>
			</td>
			<td><?php echo \PP\Helper::formatXPY($t['balance']) ?></td>
		</tr>
		<?php } ?>
	</table>

	<?php }  ?>

</div>