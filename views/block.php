<?php
$block = $this->getData('block');
$hash = $this->getData('hash');

if ($block != null) {
	$transactions = $this->getData('transactions');
	$totalIn = 0;

	foreach($transactions as $k => $transaction) {
		foreach($transaction['vin'] as $vin) {
			$totalIn += $vin['value'];
		}
	}


	// not idea to be looping here...
	// options..1) do calculations before getting here.. (other loops within)
	//			2) do calculations of 'valuein' during import.. (i like this idea best)


	$created = $block['valueout'] - $totalIn;

} else {
	?>

	<div class="my-template">

	<?php $this->render('page_header'); ?>


	<div class="blockTable">
		<h2>Unknown Block Hash </h2>


		<div class="alert alert-danger" role="alert">
			<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
			<span class="sr-only">Error:</span>
			<?php echo $hash; ?>
		</div>


	</div>


	</div>

	<?php
	return;
}
?>

<div class="my-template">

	<?php $this->render('page_header'); ?>


	<div class="blockTable">
		<h2>Details for Block # <?php echo $block['height']; ?></h2>
	</div>
	<table class="table blockTable" align="center">
		<tr>
			<td>Hash</td>
			<td>
				<?php if ($block['height'] > 1) { ?>
					<a href="<?php echo $block['previousblockhash']; ?>"><div class="glyphicon glyphicon-chevron-left"></div></a>
				<?php } ?>
				<code><?php echo $this->getData('hash'); ?></code>
				<a href="<?php echo $block['nextblockhash']; ?>"><div class="glyphicon glyphicon-chevron-right"></div></a>
			</td>
		</tr>
		<tr>
			<td>Date/Time</td><td><?php echo $block['time']; ?> extracted by
			<?php

			if (strstr($block['flags'], 'stake') == false) {
				echo $transactions[0]['vout'][0]['address'];
			} else {?>
				Proof of Stake
			<?php
			}
			?>


			</td>
		</tr>
		<tr>
			<td>Transactions</td><td><?php echo $block['transactions']; ?></td>
		</tr>
		<tr>
			<td>Value Out</td><td><?php echo \PP\Helper::formatXPY($block['valueout']); ?> XPY</td>
		</tr>
		<tr>
			<td>Difficulty</td><td><?php echo $block['difficulty']; ?></td>
		</tr>
		<tr>
			<td>Outstanding</td><td> --- XPY</td>
		</tr>


		<?php //if (strstr($block['flags'], 'Stake') == false) { ?>
		<tr>
			<td><strong>Created</strong></td><td><?php echo \PP\Helper::formatXPY($created); ?> XPY</td>
		</tr>
		<?php //} ?>

	</table>

	<ul class="nav nav-tabs">
		<li role="presentation" class="active" id="transactions"><a data-toggle="tab" href="#transactions">Transactions</a></li>
		<li role="presentation" id="raw"><a href="#raw" data-toggle="tab">Raw Block</a></li>
	</ul>

	<div class="blockTransactions" id="blockTransactions">
		<table class="blockTable table">
			<tr>
				<th>Hash</th>
				<th>Value Out</th>
				<th>From (amount)</th>
				<th>To (amount)</th>
				<th></th>
			</tr>

			<?php foreach($transactions as $k => $transaction) { ?>
				<tr>
					<td><div class="addr"><a href="/transaction/<?php echo $transaction['txid']; ?>"><code><?php echo $transaction['txid']; ?></code></a></div></td>
					<td><?php
						$total = 0;
						foreach ($transaction['vout'] as $out) {
							$total += $out['value'];
						}
						echo \PP\Helper::formatXPY($total); ?>XPY
					</td>
					<td>
						<table style="width: 100%">
						<?php
						if ($k == 0) {
							echo 'Generation + Fees';
						} else {
							foreach($transaction['vin'] as $in) { ?>
							<tr>
								<td><a href="/address/<?php echo $in['address'] ?>"><?php echo $in['address'] ?></a></td>
								<td><?php echo \PP\Helper::formatXPY($in['value']); ?> XPY</td>
							</tr>
							<?php
							}
						}
						?>
					</table>
					</td>
					<td>
						<table style="width: 100%">
							<?php foreach ($transaction['vout'] as $i => $out) {
								if ($out['type'] == 'nonstandard' && $i == 0 && $k == 0) {
									echo "<tr><td>Included in following transaction(s)</td>"
										. "<td class='text-right'>"
										. \PP\Helper::formatXPY($created)." XPY</td>";
									continue;
								}
								if (empty($out['address'])) {
									continue;
								}
								?>

							<tr>
								<td><a href="/address/<?php echo $out['address'] ?>"><?php echo $out['address'] ?></a></td>
								<td class="text-right"><?php echo \PP\Helper::formatXPY($out['value']) ?> XPY</td>
							</tr>
							<?php } ?>
						</table>


					</td>
					<td class="text-right"></td>
				</tr>
			<?php } ?>


		</table>
	</div>

	<div class="raw" id="blockRaw" style="display: none">
		<pre><?php echo json_encode(unserialize($block['raw']), JSON_PRETTY_PRINT); ?></pre>
	</div>

</div>



<script>

	var hash = <?php echo json_encode($this->getData('hash')); ?>;

	$("#transactions a").click( function() {
		$("#blockTransactions").show();
		$("#blockRaw").hide();
	});
	$("#raw a").click( function() {
		$("#blockTransactions").hide();
		$("#blockRaw").show();
	});


	//	$.ajax({
//		url: "/api/blockhash/" + hash,
//		context: document.body
//	}).done(function(data) {
//			console.log(data)

//			$.each(data, function(index, value) {
//
//				var flags = value['flags'];
//				var pos = flags.search('proof-of-stake');
//				var extractedBy = 'Proof of Work';
//				if (pos == 1) {
//					extractedBy = 'Proof of Stake';
//				}
//
//				$('#latestTransactions').append( "<tr id=\"tr_" + value['hash'] +"\"></tr>" );
//				$('#tr_' + value['hash']).append( "<td>" + value['height'] +"</td>" )
//					.append( "<td>" + value['time'] +"</td>" )
//					.append( "<td>" + value['transactions'] +"</td>" )
//					.append( "<td>" + value['valueout'] +" XPY</td>" )
//					.append( "<td>" + value['difficulty'] +"</td>" )
//					.append( "<td>" + extractedBy +"</td>" );
//
//			});


//
//		});
</script>

