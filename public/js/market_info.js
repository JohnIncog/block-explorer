function addCommas(nStr) {
    nStr += '';
    x = nStr.split('.');
    x1 = x[0];
    x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + ',' + '$2');
    }
    return x1 + x2;
}

function convertUTCDateToLocalDate(date) {
    var newDate = new Date(date.getTime()+date.getTimezoneOffset()*60*1000);

    var offset = date.getTimezoneOffset() / 60;
    var hours = date.getHours();

    newDate.setHours(hours - offset);

    return newDate;
}

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

                var usdprice = parseFloat(data.price.USD).toFixed(3);
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

                    var usdprice = parseFloat(data.price.USD).toFixed(3);
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