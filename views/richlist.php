<?php
$richList = $this->getData('richList');
$distribution = $this->getData('distribution');
$limit = $this->getData('limit');
$addressTagMap = $this->getData('addressTagMap');
?>
<div class="my-template">

	<?php $this->render('page_header'); ?>

	<?php $this->render('market_info'); ?>

		<ul class="nav nav-tabs">
			<li role="presentation" ><a href="/">Latest Blocks</a></li>
			<li role="presentation"><a href="/latesttransactions">Latest Transactions</a></li>
			<li role="presentation" class="active"><a href="/richlist">Rich List</a></li>
			<li role="presentation"><a href="/primestakes">Prime Stakes</a></li>
			<li role="presentation"><a href="/about">About</a></li>
			<li class="pull-right">
				<form method="post">
					<div class="form-group col-sm-8" style="margin-bottom: 0px">
						<select name="limit" class="form-control">
							<option value="25">Top 25</option>
							<option value="100" <?php if ($limit == 100) { echo 'selected'; } ?> >Top 100</option>
							<option value="100" <?php if ($limit == 1000) { echo 'selected'; } ?> >Top 1000</option>
						</select>
					</div>
					<div class="col-sm-2 form-group" style="margin-bottom: 0px">
						<input type="submit" value="Go" class="btn btn-default">
					</div>
				</form>
			</li>
		</ul>




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


