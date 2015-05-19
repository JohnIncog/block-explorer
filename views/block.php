<?php
$block = $this->getData('block');

$transactions = $this->getData('transactions');
//echo '<pre>';
//var_dump($block);
//echo '</pre>';
//@todo FROM  are WRONG... same as TO...
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
				<a href="<?php echo $block['previousblockhash']; ?>"><div class="glyphicon glyphicon-chevron-left"></div></a>
				<code><?php echo $this->getData('hash'); ?></code>
				<a href="<?php echo $block['nextblockhash']; ?>"><div class="glyphicon glyphicon-chevron-right"></div></a>
			</td>
		</tr>
		<tr>
			<td>Date/Time</td><td><?php echo $block['time']; ?> extracted by
			<?php if (strstr($block['flags'], 'Stake') == false) {
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
			<td><strong>Created</strong></td><td><?php echo \PP\Helper::formatXPY(0.0); ?>?? XPY</td>
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
							<?php foreach ($transaction['vout'] as $out) { ?>
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


			<!-- tr>
				<td>dasdas</td>
				<td>0 XPY</td>
				<td>
					<table style="width: 100%">
						<tr>
							<td>P9awV6nvXLgab6oxtowJjYAQf3TkqPAnA5</td><td class="text-right">26.546023 XPY</td>
						</tr>
						<tr>
							<td>P9awV6nvXLgab6oxtowJjYAQf3TkqPAnA5</td><td class="text-right">26.546023 XPY</td>
						</tr>

					</table>
				</td>
				<td colspan="2">
					<table style="width: 100%">
						<tr>
							<td>P9awV6nvXLgab6oxtowJjYAQf3TkqPAnA5</td><td class="text-right">26.546023 XPY</td>
						</tr>
					</table>
				</td>
			</tr-->

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

