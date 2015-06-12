
	<div class="my-template">

		<?php $this->render('page_header'); ?>

		<div class="alert alert-success alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			Alpha Version.  Data refreshes every 60 seconds.
		</div>

		<?php $this->render('market_info'); ?>


		<ul class="nav nav-tabs">
			<li role="presentation" class="active"><a href="/">Latest Blocks</a></li>
			<li role="presentation"><a href="/latesttransactions">Latest Transactions</a></li>
			<li role="presentation"><a href="/richlist">Rich List</a></li>
			<li role="presentation"><a href="/primestakes">Prime Stakes</a></li>
			<li role="presentation"><a href="/about">About</a></li>
		</ul>

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








