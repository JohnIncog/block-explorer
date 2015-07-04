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
                min: 0,
                minColor: '#086B8E',
                maxColor: '#01151C',
                stops: [
                    [0, '#086B8E'],
                    [0.67, '#044055'],
                    [1, '#01151C']
                ]
            },

            series : [{
                data : cdata,
                mapData: Highcharts.maps['custom/world-highres'],
                joinBy: 'hc-key',
                nullColor: '#086B8E',
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