<?php
$block = $this->getData('block');
$hash = $this->getData('hash');

if ($block != null) {
	$transactions = $this->getData('transactions');
	$created = $block['mint'];
	$destroyed = (int)$block['txFees'];
	$created += $block['txFees'];

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


	<div class="text-left">
		<h2>Details for Block # <?php echo $block['height']; ?></h2>
	</div>
	<table class="table infoTable" align="center">
		<tr>
			<td>Hash</td>
			<td>
				<?php if ($block['height'] > 1) { ?>
					<a href="<?php echo $block['previousblockhash']; ?>"><div class="glyphicon glyphicon-chevron-left"></div></a>
				<?php } ?>
				<code><?php echo $this->getData('hash'); ?></code>
				<?php if (!empty($block['nextblockhash'])) { ?>
					<a href="<?php echo $block['nextblockhash']; ?>"><div class="glyphicon glyphicon-chevron-right"></div></a>
				<?php } ?>
			</td>
		</tr>
		<tr>
			<td>Date/Time</td><td><?php echo \lib\Helper::getLocalDateTime($block['time']); ?> extracted by
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
			<td>Value Out</td><td><?php echo \lib\Helper::formatXPY($block['valueout']); ?></td>
		</tr>
		<tr>
			<td>Difficulty</td><td><?php echo $block['difficulty']; ?></td>
		</tr>
		<tr>
			<td>Outstanding</td><td><?php echo \lib\Helper::formatXPY($block['outstanding']); ?></td>
		</tr>


		<?php //if (strstr($block['flags'], 'Stake') == false) { ?>
		<?php if ($created > 0) { ?>
			<tr>
				<td><strong>Created</strong></td><td><?php echo \lib\Helper::formatXPY($created); ?></td>
			</tr>
		<?php } ?>
		<?php if ($destroyed < 0) { ?>
			<tr>
				<td><strong>Destroyed</strong></td><td><?php echo \lib\Helper::formatXPY($destroyed); ?></td>
			</tr>
		<?php } ?>
		<?php
		//} ?>

	</table>

	<ul class="nav nav-tabs">
		<li role="presentation" class="active" id="transactions"><a data-toggle="tab" href="#transactions">Transactions</a></li>
		<li role="presentation" id="raw"><a href="#raw" data-toggle="tab">Raw Block</a></li>
	</ul>

	<div class="blockTransactions" id="blockTransactions">
		<table class="blockTable table table-hover">
			<thead>
			<tr>
				<th>Hash</th>
				<th class="text-right">Value Out</th>
				<th>From (amount)</th>
				<th>To (amount)</th>
				<th></th>
			</tr>
			</thead>
			<?php foreach($transactions as $k => $transaction) { ?>
				<tr>
					<td>
						<?php echo \lib\Helper::getTxHashLink($transaction['txid'])?>
					</td>
					<td class="text-right"><?php
						$total = 0;
						foreach ($transaction['vout'] as $out) {
							$total += $out['value'];
						}
						echo \lib\Helper::formatXPY($total); ?>
					</td>
					<td>
						<table style="width: 100%">
						<?php
						if ($k == 0) {
							echo 'Generation + Fees';
						} else {
							foreach($transaction['vin'] as $in) { ?>
							<tr>
								<td>
									<?php echo \lib\Helper::getAddressLink($in['address']); ?>
								</td>
								<td class="text-right"><?php echo \lib\Helper::formatXPY($in['value']); ?></td>
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
										. \lib\Helper::formatXPY($created)."</td>";
									continue;
								}
								if (empty($out['address'])) {
									continue;
								}
								?>

							<tr>
								<td><?php echo \lib\Helper::getAddressLink($out['address']); ?></td>
								<td class="text-right"><?php echo \lib\Helper::formatXPY($out['value']) ?></td>
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
</script>

