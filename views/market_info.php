<div class="row">
	<div class="col-md-3">
		<div class="panel panel-default">
			<div class="panel-heading">Price USD</div>
			<div class="panel-body" >
				<a href="https://xpymarket.com/" target="_blank"><span id="price-usd">$0.00 USD</span></a>
			</div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="panel panel-default">
			<div class="panel-heading">Price BTC</div>
			<div class="panel-body">
				<a href="https://xpymarket.com/" target="_blank"><span id="price-btc">0 BTC</span></a>
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

	$( document ).ready(function() {



		$(function () {

			var usdprice = $.jStorage.get('usd-price');
			var btcprice = $.jStorage.get('btc-price');
			var marketcap = $.jStorage.get('market-cap');

			if (usdprice) {
				$("#market-cap").text("$" + marketcap + " USD");
				$("#price-usd").text("$" + usdprice + " USD");
				$("#price-btc").text(btcprice + " BTC");
				return false;
			}

			var url = 'https://xpymarket.com/api/info';
			$.ajax({
				url: url,
				type: "GET",
				cache: true,
				success: function(data) {
					console.log("market info");
//					console.log(data);

					var usdprice = parseFloat(data.price.USD).toFixed(2);
					var btcprice = parseFloat(data.price.BTC).toFixed(8);
					var marketcap = addCommas(parseFloat(data.market.market_cap_usd).toFixed(2));

					$.jStorage.set('usd-price', usdprice, {TTL: 60000});
					$.jStorage.set('btc-price', btcprice, {TTL: 60000});
					$.jStorage.set('market-cap', marketcap, {TTL: 60000});

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
					url: "https://xpymarket.com/api/info",
					cache: true,
					success: function(data) {
						console.log("market info");
	//						console.log(data);

						var usdprice = parseFloat(data.price.USD).toFixed(2);
						var btcprice = parseFloat(data.price.BTC).toFixed(8);
						var marketcap = addCommas(parseFloat(data.market.market_cap_usd).toFixed(2));

						$.jStorage.set('usd-price', usdprice, {TTL: 60000});
						$.jStorage.set('btc-price', btcprice, {TTL: 60000});
						$.jStorage.set('market-cap', marketcap, {TTL: 60000});

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
	});

</script>