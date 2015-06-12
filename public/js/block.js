$( document ).ready(function() {

    $("#transactions a").click( function() {
        $("#blockTransactions").show();
        $("#blockRaw").hide();
    });
    $("#raw a").click( function() {
        $("#blockTransactions").hide();
        $("#blockRaw").show();
    });
});
