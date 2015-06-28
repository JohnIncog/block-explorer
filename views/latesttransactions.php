<?php
$transactions = $this->getData('transactions');
$limit = $this->getData('limit');
$addressTagMap = $this->getData('addressTagMap');
?>
<div class="my-template">

	<?php $this->render('page_header'); ?>

	<?php $this->render('market_info'); ?>

	<?php $this->render('tabs'); ?>
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
				<td><time class="timeago" datetime="<?php echo date('c', $transaction['time']); ?>"
						><?php echo date('c', $transaction['time']); ?></time></td>
				<td><?php echo \lib\Helper::getTxHashLink($transaction['txid']) ?></td>
				<td class="text-right">
					<?php
					if ($transaction['value'] > 0) {
						echo '<span class="addressReceive">+ ';
					} else {
						echo '<span class="addressSend">';
					}
					echo \lib\Helper::formatXPY($transaction['value']) ?></span>
				</td>
				<td ><?php echo \lib\Helper::getAddressLink($transaction['address'], $addressTagMap) ?></td>
			</tr>
		<?php } ?>
	</table>

</div>
