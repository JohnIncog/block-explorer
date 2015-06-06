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

<script>
	$(function () {
		var url = 'https://coinmarketcap-nexuist.rhcloud.com/api/xpy';
		$.ajax({
			url: url,
			type: "GET",
			cache: true,
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
	});

	(function marketPoll() {
		setTimeout(function() {
			$.ajax({
				url: "https://coinmarketcap-nexuist.rhcloud.com/api/xpy",
				cache: true,
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