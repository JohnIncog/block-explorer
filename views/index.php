
	<div class="my-template">

		<?php $this->render('page_header'); ?>

		<div class="row">
			<div class="col-md-3">
				<div class="panel panel-default">
					<div class="panel-heading">--</div>
					<div class="panel-body">
						--
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="panel panel-default">
					<div class="panel-heading">--</div>
					<div class="panel-body">
						--
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="panel panel-default">
					<div class="panel-heading">--</div>
					<div class="panel-body">
						--
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="panel panel-default">
					<div class="panel-heading">Outstanding</div>
					<div class="panel-body" id="outstanding">
						0 XPY
					</div>
				</div>
			</div>
		</div>

		<ul class="nav nav-tabs">
			<li role="presentation" class="active"><a href="/">Latest Blocks</a></li>
			<li role="presentation"><a href="/latesttransactions">Latest Transactions</a></li>
			<li role="presentation"><a href="/richlist">Rich List</a></li>
			<li role="presentation"><a href="/primestakes">Prime Stakes</a></li>
		</ul>


			<table id="latestTransactions" class="table-striped table latestTransactions" align="center">
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



<script>



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
					var date = new Date(value['time']);
					var date = date.toString().replace(/GMT.*/g,'');

					$("#latestTransactions tbody").append( "<tr id=\"tr_" + value['hash'] +"\"></tr>" );
					$('#tr_' + value['hash']).append( "<td><a href=\"/block/"+value['hash']+"\">" + value['height'] +"</a></td>" )
						.append( "<td>" + jQuery.timeago(date) + "</td>" )
						.append( "<td>" + value['transactions'] +"</td>" )
						.append( "<td>" + addCommas((value['valueout']*1).toString()) +" XPY</td>" )
						.append( "<td>" + value['difficulty'] +"</td>" )
						.append( "<td>" + extractedBy + "</td>" );
					$('#tr_' + value['hash']).click( function() {
						window.location.href='/block/' + value['hash'];
					} );
					$("#outstanding").text(addCommas((value['outstanding']*1).toString()) + ' XPY');


				});

		});

		(function poll() {
			setTimeout(function() {
				$.ajax({
					url: "/api/latestblocks",
					type: "GET",
					success: function(data) {
						console.log("polling");

						$.each(data, function(index, value) {

							var extractedBy = value['flags'];
							var extractedBy = extractedBy.replace('stake-modifier', " ");
							var extractedBy = extractedBy.replace(/-/g, " ");
							var extractedBy = ucwords(extractedBy);
							var date = new Date(value['time']);
							var date = date.toString().replace(/GMT.*/g,'');

							//check if

							if ( !$('#tr_' + value['hash']).length ) {
								console.log('new block!');

								$("#latestTransactions tbody").prepend( "<tr id=\"tr_" + value['hash'] +"\"></tr>" );
								$('#tr_' + value['hash']).html( "<td><a href=\"/block/"+value['hash']+"\">" + value['height'] +"</a></td>" )
									.append( "<td>" + jQuery.timeago(date) + "</td>" )
									.append( "<td>" + value['transactions'] +"</td>" )
									.append( "<td>" + addCommas((value['valueout']*1).toString()) +" XPY</td>" )
									.append( "<td>" + value['difficulty'] +"</td>" )
									.append( "<td>" + extractedBy + "</td>" );
								$('#tr_' + value['hash']).click( function() {
									window.location.href='/block/' + value['hash'];
								} );

								$("#outstanding").text(addCommas((value['outstanding']*1).toString()) + ' XPY');
							}




						});


						//console.log(data);
						//console.log($("#latestTransactions tr:first td").val())
						//console.log($("#latestTransactions tr:last"))
					},
					dataType: "json",
					complete: poll,
					timeout: 2000
				})
			}, 20000);
		})();



	</script>






