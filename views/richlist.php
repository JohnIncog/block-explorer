<?php
$richList = $this->getData('richList');
?>
<div class="my-template">

	<?php $this->render('page_header'); ?>

	<h1>Rich List</h1>

	<ul class="nav nav-tabs">
		<li role="presentation" ><a href="/">Latest Blocks</a></li>
		<li role="presentation" class="active"><a href="/richlist">Rich List</a></li>

	</ul>
	<table class="table blockTable table-striped blockTable">
		<tr>
			<th class="text-right" style="width: 45px">Rank</th>
			<th>Address</th>
			<th>Balance</th>
			<th>Percent of coins</th>
		</tr>
		<?php foreach($richList as $rich) { ?>
			<tr>
				<td class="text-right"><?php echo $rich['rank'] ?></td>
				<td><?php echo \PP\Helper::getAddressLink($rich['address']) ?></td>
				<td><?php echo \PP\Helper::formatXPY($rich['balance']) ?></td>
				<td><?php echo $rich['percent'] ?> %</td>
			</tr>
		<?php } ?>
	</table>

</div>