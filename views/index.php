
	<div class="my-template">

		<?php $this->render('page_header'); ?>

		<div class="row">
			<div class="col-md-3">
				<div class="panel panel-default">
					<div class="panel-heading">Price USD</div>
					<div class="panel-body" >
						<a href="http://coinmarketcap.com/currencies/paycoin2/" target="_blank"><span id="price-usd">$0.00 USD</span></a>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="panel panel-default">
					<div class="panel-heading">Price BTC</div>
					<div class="panel-body">
						<a href="http://coinmarketcap.com/currencies/paycoin2/" target="_blank"><span id="price-btc">0 BTC</span></a>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="panel panel-default">
					<div class="panel-heading">Market Cap</div>
					<div class="panel-body">
						<a href="http://coinmarketcap.com/currencies/paycoin2/" target="_blank"><span id="market-cap">$0 USD</span></a>
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



<script>
		var blockHeight = 0;


		$.ajax({
			url: "/api/latestblocks",
			context: document.body
		}).done(function(data) {

				console.log(data)
				blockHeight = data.data[0].height;
				$("#outstanding").text(addCommas((data.data[0]['outstanding']*1).toString()) + ' XPY');
				$.each(data.data, function(index, value) {
					
					var extractedBy = value['flags'];
					var extractedBy = extractedBy.replace('stake-modifier', " ");
					var extractedBy = extractedBy.replace(/-/g, " ");
					var extractedBy = ucwords(extractedBy);
					var date = new Date(value['time']);
					var date = date.toString().replace(/GMT.*/g,'');

					$("#latestTransactions tbody").append( "<tr id=\"tr_" + value['hash'] +"\"></tr>" );
					$('#tr_' + value['hash']).append( "<td><a href=\"/block/"+value['hash']+"\">" + value['height'] +"</a></td>" )
						.append( "<td><time class='timeago' datetime='" + date + "'>" + date + "</time></td>" )
						.append( "<td>" + value['transactions'] +"</td>" )
						.append( "<td>" + addCommas((value['valueout']*1).toString()) +" XPY</td>" )
						.append( "<td>" + value['difficulty'] +"</td>" )
						.append( "<td>" + extractedBy + "</td>" );
					$('#tr_' + value['hash']).click( function() {
						window.location.href='/block/' + value['hash'];
					} );

					jQuery("time.timeago").timeago();

				});

		});

		(function poll() {
			setTimeout(function() {
				$.ajax({
					url: "/api/latestblocks",
					type: "GET",
  					data: { height: blockHeight },
					success: function(data) {
						console.log("polling");
//						console.log(data);
						blockHeight = data.data[0].height; // Store Blockheight
						$("#outstanding").text(addCommas((data.data[0]['outstanding']*1).toString()) + ' XPY');
						$.each(data.data, function(index, value) {

							var extractedBy = value['flags'];
							var extractedBy = extractedBy.replace('stake-modifier', " ");
							var extractedBy = extractedBy.replace(/-/g, " ");
							var extractedBy = ucwords(extractedBy);
							var date = new Date(value['time']);
							var date = date.toString().replace(/GMT.*/g,'');

							//check if

							if ( !$('#tr_' + value['hash']).length ) {
								console.log('new block!');
								$("#latestTransactions tbody").prepend( "<tr style=\"background-color: #00EF00;\" id=\"tr_" + value['hash'] +"\"></tr>" );
								$('#tr_' + value['hash']).hide();
								$('#tr_' + value['hash']).html( "<td><a href=\"/block/"+value['hash']+"\">" + value['height'] +"</a></td>" )
									.append( "<td><time class='timeago' datetime='" + date + "'>" + date + "</time></td>" )
									.append( "<td>" + value['transactions'] +"</td>" )
									.append( "<td>" + addCommas((value['valueout']*1).toString()) +" XPY</td>" )
									.append( "<td>" + value['difficulty'] +"</td>" )
									.append( "<td>" + extractedBy + "</td>" );
								$('#tr_' + value['hash']).click( function() {
									window.location.href='/block/' + value['hash'];
								} );
								$('#tr_' + value['hash']).show();
								$("#latestTransactions tr:last").remove();
								$('#tr_' + value['hash']).animate({backgroundColor: 'rgba(0, 0, 0, 0)' }, 3000)
							}
							jQuery("time.timeago").timeago();



						});


						//console.log(data);
						//console.log($("#latestTransactions tr:first td").val())
						//console.log($("#latestTransactions tr:last"))
					},
					dataType: "json",
					complete: poll,
					timeout: 2000
				})
			}, 55000);
		})();

		$.ajax({
			url: "http://coinmarketcap-nexuist.rhcloud.com/api/xpy",
			type: "GET",
			success: function(data) {
				console.log("market info");
				var usdprice = parseFloat(data.price.usd).toFixed(2);
				var btcprice = parseFloat(data.price.btc).toFixed(8);
				var marketcap = addCommas(parseFloat(data.market_cap.usd).toFixed(2));
				$("#market-cap").text("$" + marketcap + " USD");
				$("#price-usd").text("$" + usdprice + " USD");
				$("#price-btc").text(btcprice + " BTC");

			},
			dataType: "json",
			timeout: 2000
		});

		(function marketPoll() {
			setTimeout(function() {
				$.ajax({
					url: "http://coinmarketcap-nexuist.rhcloud.com/api/xpy",
					type: "GET",
					success: function(data) {
						console.log("market info");
//						console.log(data);

						var usdprice = parseFloat(data.price.usd).toFixed(2);
						var btcprice = parseFloat(data.price.btc).toFixed(8);
						var marketcap = addCommas(parseFloat(data.market_cap.usd).toFixed(2));
						$("#market-cap").text("$" + marketcap + " USD");
						$("#price-usd").text("$" + usdprice + " USD");
						$("#price-btc").text(btcprice + " BTC");

					},
					dataType: "json",
					complete: marketPoll,
					timeout: 2000
				})
			}, 60000);
		})();

	</script>






