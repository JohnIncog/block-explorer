$( document ).ready(function() {

    $('#counter').countdown({
        image: "digits.png",
        format: "dd:mm:ss",
        endTime: new Date('07/01/15 00:00:00'),
        image: "/img/digits.png"
    });

    //$('#counter').countdown({
    //    stepTime: 60,
    //    format: 'hh:mm:ss',
    //    startTime: "12:32:55",
    //    digitImages: 6,
    //    digitWidth: 53,
    //    digitHeight: 77,
    //    timerEnd: function() { alert('end!!'); },
    //
    //});

});