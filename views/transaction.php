<?php
$transaction = $this->getData('transaction');
$transactionIns = $this->getData('transactionIns');
$transactionOuts = $this->getData('transactionOuts');
$totalOut = 0;
foreach ($transactionOuts as $t) {
	$totalOut += $t['value'];
}
echo "<pre>";
var_dump($transaction);
var_dump($transactionIns);
var_dump($transactionOuts);
echo "</pre>";
?>


<div class="my-template">

	<?php $this->render('page_header'); ?>


	<div class="blockTable">
		<h2>Details for Transaction</h2>
	</div>
	<table class="table blockTable" align="center">
		<tr>
			<td>Hash</td>
			<td><code><?php echo $transaction['txid']; ?></code></td>
		</tr>
		<tr>
			<td>Block Height</td>
			<td><?php echo $transaction['block_height']; ?></td>
		</tr>
		<tr>
			<td>Block Date/Time</td>
			<td><?php echo \PP\Helper::formatTime($transaction['time']); ?></td>
		</tr>
		<tr>
			<td>Total Output</td>
			<td><?php echo \PP\Helper::formatXPY($totalOut); ?> XPY</td>
		</tr>
		<tr>
			<td>Proof of Stake + Fees</td>
			<td><?php echo \PP\Helper::formatXPY($totalOut); ?> XPY</td>
		</tr>

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
			<?php foreach ($transactionIns as $i => $transactionIn) { ?>

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
						<?php } else { ?>
							<a href="/wallet/<?php echo $transactionIn['address'] ?>"><?php echo $transactionIn['address'] ?></a>
						<?php } ?>
					</td>
					<td class="text-right">
						<?php
						$value = $transactionIn['value'];
						if ($i == 0 && empty($transactionIn['txid'])) {
							$value = $totalOut;
						} ?>

						<?php echo \PP\Helper::formatXPY($value); ?> XPY
					</td>
				</tr>


			<?php } ?>
<!--			<tr>-->
<!--				<td>0</td>-->
<!--				<td>Generation + Fees</td>-->
<!--				<td>N/A</td>-->
<!--				<td class="text-right">--><?php //echo \PP\Helper::formatXPY($totalOut); ?><!-- XPY</td>-->
<!--			</tr>-->
		</table>

		<h3>Outputs</h3>
		<table class="table">
			<tr>
				<th>Index</th>
				<th>Redeemed in</th>
				<th>Address</th>
				<th class="text-right">Amount</th>
			</tr>
			<?php foreach ($transactionOuts as $i => $transactionOut) { ?>
			<tr>
				<td><?php echo $i ?></td>
				<td> --- </td>
				<td><a href="/wallet/<?php echo $transactionOut['address'] ?>"><?php echo $transactionOut['address'] ?></a></td>
				<td class="text-right"><?php echo \PP\Helper::formatXPY($transactionOut['value'])?> XPY</td>

			</tr>
			<?php } ?>
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