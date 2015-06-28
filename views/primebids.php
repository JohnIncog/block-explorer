<?php
$bids = $this->getData('primeBids');
$limit = $this->getData('limit');
$addressTagMap = $this->getData('addressTagMap');
$primeBidders = $this->getData('primeBidders');
$startDate = $this->getData('startDate');
$currentRound = $this->getData('currentRound');
?>
<div class="my-template">

	<?php $this->render('page_header'); ?>

	<?php $this->render('market_info'); ?>

	<?php $this->render('tabs'); ?>

	<div id="counter"></div>

	<table class="table infoTable table-invert" >
		<thead>
			<th>Prime Bidding Information</th>
		</thead>
		<tr>
			<td><strong>Minimum Bid Amount</strong></td>
			<td><?php echo \lib\Helper::formatXPY(PRIME_BID_AMOUNT); ?></td>
		</tr>
		<tr>
			<td><strong>Number of Bidders</strong></td>
			<td><?php echo count($bids); ?></td>
		</tr>
		<tr>
			<td><strong>Number of Qualified Bidders</strong></td>
			<td><?php echo $primeBidders; ?></td>
		</tr>
		<tr>
			<td><strong>Prime Bid Start Date</strong></td>
			<td><?php echo date('F d Y', $startDate); ?></td>
		</tr>
		<tr>
			<td><strong>Current Round</strong></td>
			<td><?php echo $currentRound; ?></td>
		</tr>
		<tr>
			<td><strong>Number of Primes Per Round</strong></td>
			<td>2</td>
		</tr>

	</table>

	<table class="table infoTable table-invert table-hover">
		<thead>
		<tr>
			<th>Bid Rank</th>
			<th>Address</th>
			<th>Bid</th>
			<th>Percent</th>
			<th>Rich List Rank</th>
			<th class="text-right">Balance</th>
		</tr>
		</thead>
		<?php if (count($bids) == 0) { ?>
			<tr>
				<td colspan="6" class="text-center">
					No Bids found.
				</td>
			</tr>
		<?php } ?>
		<?php foreach($bids as $bids) { ?>
			<tr>
				<td ><?php echo $bids['bidrank'] ?></td>
				<td ><?php echo \lib\Helper::getAddressLink($bids['address'], $addressTagMap) ?></td>
				<td ><?php echo \lib\Helper::formatXPY($bids['bid'], $addressTagMap) ?></td>
				<td ><?php echo $bids['percent'] ?> %</td>
				<td ><?php echo $bids['rank'] ?></td>
				<td class="text-right"><?php echo \lib\Helper::formatXPY($bids['balance'], $addressTagMap) ?></td>
			</tr>
		<?php } ?>
	</table>

</div>
