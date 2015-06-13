
$( document ).ready(function() {

    $.getJSON(chartdata, function (data) {
        // Create the chart
        $('#container').highcharts('StockChart', {
            chart: {
                zoomType: 'x'
            },

            rangeSelector: {
                buttons : [{
                    type : 'day',
                    count : 1,
                    text : '1d'
                }, {
                    type : 'week',
                    count : 1,
                    text : '1w'
                }, {
                    type : 'month',
                    count : 1,
                    text : '1m'
                }, {
                    type : 'ytd',
                    text : 'YTD'
                }, {
                    type: 'year',
                    count: 1,
                    text: '1y'
                }, {
                    type : 'all',
                    count : 1,
                    text : 'All'
                }],
                selected: 1
            },

            title: {
                text: chart
            },

            series: [
                {
                    name: 'XPY',
                    data: data,
                    threshold : null,
                    type : 'line',
                    tooltip: {
                        valueDecimals: 6
                    }



                }
            ]
        });

    });
});