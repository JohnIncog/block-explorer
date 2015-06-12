$(document).ready(function () {


    $.ajax({
        url: "/api/latestblocks?limit=1",
        context: document.body
    }).done(function (data) {

            console.log(data)
            blockHeight = data.data[0].height;
            $("#outstanding").text(addCommas((data.data[0]['outstanding'] * 1).toString()) + ' XPY');


        });

    (function poll() {
        setTimeout(function () {
            $.ajax({
                url: "/api/latestblocks",
                type: "GET",
                data: { height: blockHeight },
                success: function (data) {
                    console.log("polling");
                    //						console.log(data);
                    blockHeight = data.data[0].height; // Store Blockheight
                    $("#outstanding").text(addCommas((data.data[0]['outstanding'] * 1).toString()) + ' XPY');

                },
                dataType: "json",
                complete: poll,
                timeout: 2000
            })
        }, 55000);
    })();
});
