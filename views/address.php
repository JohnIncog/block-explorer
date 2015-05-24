<?php
$address = $this->getData('address');
$addressInformation = $this->getData('addressInformation');
?>


<div class="my-template">

	<?php $this->render('page_header'); ?>

	<h1>Wallet Details</h1>
	<h2>This page is not working properly... </h2>

	<table class="table blockTable">
		<tr>
			<td>Address</td><td><?php echo \PP\Helper::getAddressLink($address); ?></td>
		</tr>
		<tr>
			<td>Balance</td>
			<td><?php echo \PP\Helper::formatXPY($addressInformation['balance']); ?></td>
		</tr>
		<tr>
			<td>Received</td>
			<td><?php echo \PP\Helper::formatXPY($addressInformation['totalInValue']); ?> in <?php echo $addressInformation['totalInTransactions'] ?> transactions</td>
		</tr>
		<tr>
			<td>Sent</td>
			<td><?php echo \PP\Helper::formatXPY($addressInformation['totalOutValue']); ?> in <?php echo $addressInformation['totalOutTransactions'] ?> transactions</td>
		</tr>
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
			<td><?php echo \PP\Helper::getTxHashLink($t['txidp']) ?></td>
			<td><?php echo $t['block_height'] ?></td>
			<td><?php echo \PP\Helper::formatTime($t['time']) ?></td>
			<td>
				<?php
				if ($t['value'] > 0) {
					echo '+';
				}
				?>
				<?php echo \PP\Helper::formatXPY($t['value']);
				if ($t['type'] == 'Stake') {
					echo ' <span class="label label-info">Stake</span>';
				}

				?>
			</td>
			<td><?php echo \PP\Helper::formatXPY($t['balance']), $t['type'] ?></td>
		</tr>
		<?php } ?>
	</table>

</div>