
	<div class="my-template">

		<?php $this->render('page_header'); ?>


		<ul class="nav nav-tabs">
			<li role="presentation" class="active"><a href="#">Latest Blocks</a></li>
			<li role="presentation"><a href="#">What</a></li>
			<li role="presentation"><a href="#">Will</a></li>
			<li role="presentation"><a href="#">I</a></li>
			<li role="presentation"><a href="#">Do</a></li>
			<li role="presentation"><a href="#">Here</a></li>
		</ul>

			<table id="latestTransactions" class="table-striped table latestTransactions" align="center">
				<tr>
					<th>Height</th>
					<th>Time</th>
					<th>Transactions</th>
					<th>Value Out</th>
					<th>Difficulty</th>
					<th>Extracted By</th>
				</tr>
			</table>

	</div>



<script>
		$.ajax({
			url: "/api/latestblocks",
			context: document.body
		}).done(function(data) {
				console.log(data)

				$.each(data, function(index, value) {

					var flags = value['flags'];
					var pos = flags.search('proof-of-stake');
					var extractedBy = 'Proof of Work';
					if (pos == 1) {
						extractedBy = 'Proof of Stake';
					}

					$('#latestTransactions').append( "<tr class=\"blockclick\" id=\"tr_" + value['hash'] +"\"></tr>" );
					$('#tr_' + value['hash']).append( "<td><a href=\"/block/"+value['hash']+"\">" + value['height'] +"</a></td>" )
						.append( "<td>" + value['time'] +"</td>" )
						.append( "<td>" + value['transactions'] +"</td>" )
						.append( "<td>" + value['valueout'] +" XPY</td>" )
						.append( "<td>" + value['difficulty'] +"</td>" )
						.append( "<td>" + extractedBy +"</td>" );

				});



		});
	</script>






