<?php
$richList = $this->getData('richList');
$distribution = $this->getData('distribution');
$limit = $this->getData('limit');
$addressTagMap = $this->getData('addressTagMap');
?>
<div class="my-template">

	<?php $this->render('page_header'); ?>

	<?php $this->render('market_info'); ?>

	<?php $this->render('tabs'); ?>





	<div class="row">
		<div class="col-md-7" style="padding-right: 0">
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
					<td><?php echo \lib\Helper::getAddressLink($rich['address'], $addressTagMap) ?></td>
					<td><?php echo \lib\Helper::formatXPY($rich['balance']) ?></td>
					<td><?php echo $rich['percent'] ?> %</td>
				</tr>
			<?php } ?>
			</table>

		</div>
		<div class="col-md-5" style="padding-left: 0">
			<table class="table infoTable table-invert table-hover" style="margin-bottom: 0;">
				<thead>
				<tr>
					<th>Top</th>
					<th>Holdings</th>
					<th>Percent</th>
				</tr>
				</thead>
				<?php
				$last = end($distribution);
				?>
				<?php foreach($distribution as &$row) {
					$row['percent'] = 0;
					if ($last['holdings'] > 0) {
						$row['percent'] = round($row['holdings']/$last['holdings']*100, 2);
					}
					?>
					<tr>
						<td><?php echo $row['top'] ?></td>
						<td><?php echo \lib\Helper::formatXPY($row['holdings']) ?></td>
						<td><?php echo $row['percent'] ?> %</td>
					</tr>
				<?php }?>
			</table>


<!--			<div id="container" style="min-width: 310px; height: 400px; max-width: 600px; margin: 0 auto"></div>-->

		</div>
	</div>
</div>


