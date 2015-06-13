<?php
$primeStakes = $this->getData('primeStakes');
$limit = $this->getData('limit');
$addressTagMap = $this->getData('addressTagMap');

?>
<div class="my-template">

	<?php $this->render('page_header'); ?>

	<?php $this->render('market_info'); ?>

	<?php $this->render('tabs'); ?>

	<table class="table latestTransactions table-invert table-hover">
		<thead>
		<tr>
			<th>Height</th>
			<th>Transaction ID</th>
			<th>Address</th>
			<th>Value</th>
			<th>Time</th>
		</tr>
		</thead>
		<?php foreach($primeStakes as $primeStake) { ?>
			<tr>
				<td><a href="/block/<?php echo $primeStake['hash']?>"><?php echo $primeStake['block_height'] ?></td>
				<td><?php echo \lib\Helper::getTxHashLink($primeStake['txid']) ?></td>
				<td><?php echo \lib\Helper::getAddressLink($primeStake['address'], $addressTagMap) ?></td>
				<td><?php echo \lib\Helper::formatXPY($primeStake['value']) ?></td>
				<td><?php echo \lib\Helper::formatTime($primeStake['time']) ?></td>
			</tr>
		<?php } ?>
	</table>

</div>

