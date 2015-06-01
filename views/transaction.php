<?php
$transaction = $this->getData('transaction');
$redeemedIn = $this->getData('redeemedIn');
$transactionsIn = $this->getData('transactionsIn');
$transactionsOut = $this->getData('transactionsOut');
$totalOut = 0;
$totalIn = 0;
foreach ($transactionsIn as $t) {
	$totalIn += $t['value'];
}
foreach ($transactionsOut as $t) {
	$totalOut += $t['value'];
}
$created = $totalOut - $totalIn;
?>


<div class="my-template">

	<?php $this->render('page_header'); ?>



	<h2 class="text-left">Details for Transaction</h2>

	<table class="table infoTable" align="center">
		<tr>
			<td>Hash</td>
			<td><code><?php echo $transaction['txid']; ?></code></td>
		</tr>
		<tr>
			<td>Block Height</td>
			<td><a href="/block/<?php echo $transaction['hash']; ?>"><?php echo $transaction['block_height']; ?></a></td>
		</tr>
		<tr>
			<td>Block Date/Time</td>
			<td><?php echo \lib\Helper::formatTime($transaction['time']); ?></td>
		</tr>
		<tr>
			<td>Total Output</td>
			<td><?php echo \lib\Helper::formatXPY($totalOut); ?></td>
		</tr>
		<?php if (stristr($transaction['flags'], 'proof-of-stake') !== false) { ?>
		<tr>
			<td><?php if ($created > 0) {
				echo 'Proof of Stake + ';
				} else {
					$created = abs($created);
				}
				?>
				Fees</td>
			<td><?php echo \lib\Helper::formatXPY($created); ?></td>
		</tr>
		<?php } ?>
		<?php if (stristr($transaction['flags'], 'proof-of-work') !== false) { ?>
			<tr>
				<td><?php if ($created > 0) {
					echo "Mined + ";
					} else {
						$created = abs($created);
					}
					?> Fees</td>
				<td><?php echo \lib\Helper::formatXPY($created); ?></td>
			</tr>
		<?php } ?>
		<?php if ($transaction['txFee'] > 0) { ?>
		<tr>
			<td>Fees</td>
			<td><?php echo \lib\Helper::formatXPY($transaction['txFee']); ?></td>
		</tr>
		<?php } ?>
	</table>




	<ul class="nav nav-tabs">
		<li role="presentation" class="active" id="transactions"><a data-toggle="tab" href="#transactions">Inputs / Outputs</a></li>
		<li role="presentation" id="raw"><a href="#raw" data-toggle="tab">Raw Block</a></li>
	</ul>

	<div class="blockTransactions" id="blockTransactions">

		<h3>Inputs</h3>
		<table class="table">
			<tr>
				<th>Index</th>
				<th>Previous output</th>
				<th>Address</th>
				<th class="text-right">Amount</th>
			</tr>
			<?php foreach ($transactionsIn as $i => $transactionIn) { ?>

				<tr>
					<td><?php echo $i ?></td>
					<td>
						<?php if ($i == 0 && empty($transactionIn['txid'])) { ?>
							Generation + Fees
						<?php } else { ?>
							<div class="hash">
								<a href="/transaction/<?php echo $transactionIn['txid'] ?>"><?php echo $transactionIn['txid'] ?></a>
							</div>
						<?php } ?>
					</td>
					<td>
						<?php if ($i == 0 && empty($transactionIn['txid'])) { ?>
							N/A
						<?php } else {
							echo \lib\Helper::getAddressLink($transactionIn['address']);
						} ?>
					</td>
					<td class="text-right">
						<?php
						$value = $transactionIn['value'];
						if ($i == 0 && empty($transactionIn['txid'])) {
							$value = $totalOut;
						} ?>

						<?php echo \lib\Helper::formatXPY($value); ?>
					</td>
				</tr>


			<?php } ?>
		</table>

		<h3>Outputs</h3>
		<table class="table">
			<tr>
				<th>Index</th>
				<th>Redeemed in</th>
				<th>Address</th>
				<th class="text-right">Amount</th>
			</tr>
			<?php
				$i = 0;
				foreach ($transactionsOut as $transactionOut) {
					if ($transactionOut['type'] == 'nonstandard') {
						continue;
					}
					?>
					<tr>
						<td><?php echo $i ?></td>
						<td><?php
							if (!empty($redeemedIn[$i]['txidp'])) {
								echo \lib\Helper::getTxHashLink($redeemedIn[$i]['txidp']);
							} else {
								echo '<i>Not yet redeemed</i>';
							}

							?></td>
						<td>
							<?php echo \lib\Helper::getAddressLink($transactionOut['address']); ?>
						</td>
						<td class="text-right"><?php echo \lib\Helper::formatXPY($transactionOut['value'])?></td>
					</tr>
					<?php
					$i++;
				}
			?>
		</table>

	</div>

	<div class="raw" id="blockRaw" style="display: none">
		<pre><?php echo json_encode(unserialize($transaction['raw']), JSON_PRETTY_PRINT); ?></pre>
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

</script>