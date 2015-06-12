$(function () {
    $('#container').highcharts({
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            backgroundColor: 'rgba(0, 0, 0, 0.2)'
        },
        title: {
            text: 'Distribution',
            style: {
                color: 'white'
            }
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                    style: {
                        color: 'white'
                    }
                }
            }
        },
        series: [{
            type: 'pie',
            name: 'Distribution',
            data: [
                ['Top 10',       43],
                ['Top 100',       47],
                ['Top 1000',    8],
                ['Other',    2]

            ]
        }]
    });
});