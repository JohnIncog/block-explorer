$( document ).ready(function() {


    jQuery('#claim-address').click( function() {
    window.location = '/tagging';
    });

    jQuery('#remove-tag').click( function() {

        $.ajax({
            url: "/api/disputeaddresstag",
            data: $('#tag-address-form').serialize(),
            method: 'post'
        }).done(function(data) {
                console.log(data.data.message);
                var alert = '<div class="alert alert-success alert-dismissible" role="alert" id="alert-1" >'
                    + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'
                    + '<strong>Great Success!</strong> ' + data.data.message
                    + '</div>';
                $(".infoTable").before(alert);
            })
    });

    jQuery('#tag-address-form').submit( function() {

        $.ajax({
            url: "/api/tagaddress",
            data: $('#tag-address-form').serialize(),
            method: 'post'
        }).done(function(data) {
                console.log(data);
                if (data.data.success) {
                    var alert = '<div class="alert alert-success alert-dismissible" role="alert" id="alert-1" >'
                        + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'
                        + '<strong>Great Success!</strong> Tag has been added to address.'
                        + '</div>';
                    $(".infoTable").before(alert);
                } else {
                    var alert = '<div class="alert alert-danger alert-dismissible" role="alert" id="alert-2" >'
                        + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'
                        + '<strong>Error!</strong> ' + data.data.error
                        + '</div>';
                    $(".infoTable").before(alert);
                }

            });
        return false;
    });

        $("#transactionTable").stupidtable();


        $('#qrcode').qrcode({
            size: 150,

            "color": "#087094",
            "text": address,
            fill: '#087094'

    });
    });