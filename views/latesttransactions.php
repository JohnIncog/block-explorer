<?php
$transactions = $this->getData('transactions');
$limit = $this->getData('limit');
$addressTagMap = $this->getData('addressTagMap');
?>
<div class="my-template">

	<?php $this->render('page_header'); ?>

	<?php $this->render('market_info'); ?>

	<ul class="nav nav-tabs">
		<li role="presentation" ><a href="/">Latest Blocks</a></li>
		<li role="presentation" class="active"><a href="/latesttransactions">Latest Transactions</a></li>
		<li role="presentation"><a href="/richlist">Rich List</a></li>
		<li role="presentation"><a href="/primestakes">Prime Stakes</a></li>
		<li role="presentation"><a href="/about">About</a></li>
		<li class="pull-right">
			<form method="post">
				<div class="form-group col-sm-8" style="margin-bottom: 0px">
					<select name="limit" class="form-control">
						<option value="25">Last 25</option>
						<option value="100" <?php if ($limit == 100) { echo 'selected'; } ?> >Last 100</option>
						<option value="1000" <?php if ($limit == 1000) { echo 'selected'; } ?> >Last 1000</option>
					</select>
				</div>
				<div class="col-sm-2 form-group " style="margin-bottom: 0px">
					<input type="submit" value="Go" class="btn btn-default">
				</div>
			</form>
		</li>
	</ul>
	<table class="table infoTable table-invert table-hover">
		<thead>
		<tr>
			<th>Time</th>
			<th>Transaction ID</th>
			<th class="text-right">Value</th>
			<th>Address</th>

		</tr>
		</thead>
		<?php foreach($transactions as $transaction) { ?>
			<tr>
				<td><time class="timeago" datetime="<?php echo date('c', $transaction['time']); ?>"
						><?php echo date('c', $transaction['time']); ?></time>
				<td><?php echo \lib\Helper::getTxHashLink($transaction['txid']) ?></td>
				<td class="text-right">
					<?php
					if ($transaction['value'] > 0) {
						echo '<span class="addressReceive">+ ';
					} else {
						echo '<span class="addressSend">';
					}
					echo \lib\Helper::formatXPY($transaction['value']) ?></span>
				</td>
				<td ><?php echo \lib\Helper::getAddressLink($transaction['address'], $addressTagMap) ?></td>
			</tr>
		<?php } ?>
	</table>

</div>
