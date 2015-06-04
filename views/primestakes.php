<?php
$primeStakes = $this->getData('primeStakes');
?>
<div class="my-template">

	<?php $this->render('page_header'); ?>

	<?php $this->render('market_info'); ?>

	<ul class="nav nav-tabs">
		<li role="presentation" ><a href="/">Latest Blocks</a></li>
		<li role="presentation"><a href="/latesttransactions">Latest Transactions</a></li>
		<li role="presentation"><a href="/richlist">Rich List</a></li>
		<li role="presentation" class="active"><a href="/primestakes">Prime Stakes</a></li>
	</ul>

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
				<td><?php echo \lib\Helper::getAddressLink($primeStake['address']) ?></td>
				<td><?php echo \lib\Helper::formatXPY($primeStake['value']) ?></td>
				<td><?php echo \lib\Helper::formatTime($primeStake['time']) ?></td>
			</tr>
		<?php } ?>
	</table>

</div>

<script>
	$.ajax({
		url: "/api/latestblocks?limit=1",
		context: document.body
	}).done(function(data) {

			console.log(data)
			blockHeight = data.data[0].height;
			$("#outstanding").text(addCommas((data.data[0]['outstanding']*1).toString()) + ' XPY');


		});

	(function poll() {
		setTimeout(function() {
			$.ajax({
				url: "/api/latestblocks",
				type: "GET",
				data: { height: blockHeight },
				success: function(data) {
					console.log("polling");
//						console.log(data);
					blockHeight = data.data[0].height; // Store Blockheight
					$("#outstanding").text(addCommas((data.data[0]['outstanding']*1).toString()) + ' XPY');

				},
				dataType: "json",
				complete: poll,
				timeout: 2000
			})
		}, 55000);
	})();
</script>