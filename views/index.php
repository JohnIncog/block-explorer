
	<div class="my-template">

		<?php $this->render('page_header'); ?>

		<div class="panel panel-default">
			<div class="panel-heading">Lorem Ipsum</div>
			<div class="panel-body">
				Some sort of graph... or some market stats...
			</div>
		</div>


		<ul class="nav nav-tabs">
			<li role="presentation" class="active"><a href="/">Latest Blocks</a></li>
			<li role="presentation"><a href="/latesttransactions">Latest Transactions</a></li>
			<li role="presentation"><a href="/richlist">Rich List</a></li>
			<li role="presentation"><a href="/primestakes">Prime Stakes</a></li>
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

	function ucwords(str) {
		//  discuss at: http://phpjs.org/functions/ucwords/
		// original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
		// improved by: Waldo Malqui Silva
		// improved by: Robin
		// improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
		// bugfixed by: Onno Marsman
		//    input by: James (http://www.james-bell.co.uk/)
		//   example 1: ucwords('kevin van  zonneveld');
		//   returns 1: 'Kevin Van  Zonneveld'
		//   example 2: ucwords('HELLO WORLD');
		//   returns 2: 'HELLO WORLD'

		return (str + '')
			.replace(/^([a-z\u00E0-\u00FC])|\s+([a-z\u00E0-\u00FC])/g, function($1) {
				return $1.toUpperCase();
			});
	}

		$.ajax({
			url: "/api/latestblocks",
			context: document.body
		}).done(function(data) {

				console.log(data)

				$.each(data, function(index, value) {

					var extractedBy = value['flags'];
					var extractedBy = extractedBy.replace('stake-modifier', " ");
					var extractedBy = extractedBy.replace(/-/g, " ");
					var extractedBy = ucwords(extractedBy);

					$('#latestTransactions').append( "<tr id=\"tr_" + value['hash'] +"\"></tr>" );
					$('#tr_' + value['hash']).append( "<td><a href=\"/block/"+value['hash']+"\">" + value['height'] +"</a></td>" )
						.append( "<td>" + value['time'] +"</td>" )
						.append( "<td>" + value['transactions'] +"</td>" )
						.append( "<td>" + value['valueout'] +" XPY</td>" )
						.append( "<td>" + value['difficulty'] +"</td>" )
						.append( "<td>" + extractedBy + "</td>" );
					$('#tr_' + value['hash']).click( function() {
						window.location.href='/block/' + value['hash'];
					} );

				});

		});
	</script>






