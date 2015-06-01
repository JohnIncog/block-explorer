<?php
$richList = $this->getData('richList');
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