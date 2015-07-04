$( document ).ready(function() {

    $(function () {
        $('#container').highcharts(options);
    });

    jQuery('.node-list').click( function($e) {
        //console.log($e);
        console.log($e.currentTarget.id);
        var subver = $e.currentTarget.id;
        $("#node-modal-title").text(subver);

        var url = '/api/nodes';
        $.ajax({
            url: url,
            type: "GET",
            data: {subversion: subver},
            cache: true,
            success: function(data) {
                $("#node-list-nodes").empty();
                $.each(data.data.nodes, function( index, node ) {
                    $("#node-list-nodes").append("addnode=" + node + "\n");
                });
            },
            dataType: "json",
            timeout: 2000
        });


    });

});