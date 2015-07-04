$( document ).ready(function() {




        // Initiate the chart
        $('#container2').highcharts('Map', {

            chart: {
            backgroundColor: '#043D51',
        },

        title : {
            text : 'Paycoin Network Map',
            style: {
                color: '#E0E0E3',
                fontFamily: '"Montserrat", sans-serif',
                textTransform: 'uppercase',
                fontSize: '25px',
            }
        },
            mapNavigation: {
                enabled: true,
                buttonOptions: {
                    verticalAlign: 'bottom'
                }
            },
            
            colorAxis: {
                min: 1,
                type: 'logarithmic',
                minColor: '#EEEEFF',
                maxColor: '#000022',
                stops: [
                    [0, '#EFEFFF'],
                    [0.67, '#4444FF'],
                    [1, '#000022']
                ]
            },

            series : [{
                data : cdata,
                mapData: Highcharts.maps['custom/world-highres'],
                joinBy: 'hc-key',
                name: 'Random data',
                states: {
                    hover: {
                        color: '#076C8D'
                    }
                },
                dataLabels: {
                    enabled: true,
                    format: '{point.name}'
                }
            }]
        });



});