
	<div class="my-template">

		<?php $this->render('page_header'); ?>

		<div class="alert alert-success alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			Beta Version.  Data refreshes every 60 seconds.
		</div>

		<?php $this->render('market_info'); ?>


		<?php $this->render('tabs'); ?>

		<table id="latestTransactions" class="table-hover table latestTransactions table-invert" align="center">
			<thead>
			<tr>
				<th>Height</th>
				<th>Time</th>
				<th>Transactions</th>
				<th>Value Out</th>
				<th>Difficulty</th>
				<th>Extracted By</th>
			</tr>
			</thead>
			<tbody>

			</tbody>
		</table>

	</div>








