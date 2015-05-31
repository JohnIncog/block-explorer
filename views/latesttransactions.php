<?php
$transactions = $this->getData('transactions');
?>
<div class="my-template">

	<?php $this->render('page_header'); ?>


	<ul class="nav nav-tabs">
		<li role="presentation" ><a href="/">Latest Blocks</a></li>
		<li role="presentation" class="active"><a href="/latesttransactions">Latest Transactions</a></li>
		<li role="presentation"><a href="/richlist">Rich List</a></li>
		<li role="presentation"><a href="/primestakes">Prime Stakes</a></li>
	</ul>
	<table class="table infoTable table-invert table-hover">
		<thead>
		<tr>
			<th>Time</th>
			<th>Transaction ID</th>
			<th class="text-right">Value</th>
			<th>Address</th>

		</tr>
		</thead>
		<?php foreach($transactions as $transaction) { ?>
			<tr>
				<td><?php echo \PP\Helper::formatTime($transaction['time'], true) ?></td>
				<td><?php echo \PP\Helper::getTxHashLink($transaction['txid']) ?></td>
				<td class="text-right">
					<?php
					if ($transaction['value'] > 0) {
						echo '<span class="addressReceive">+ ';
					} else {
						echo '<span class="addressSend">';
					}
					echo \PP\Helper::formatXPY($transaction['value']) ?></span>
				</td>
				<td ><?php echo \PP\Helper::getAddressLink($transaction['address']) ?></td>
			</tr>
		<?php } ?>
	</table>

</div>