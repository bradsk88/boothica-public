var callback = null;

function initializeWebcam(cameraDivId, base64SnapHandlerCallback) {
    $("#webcam_loading_placeholder").css('display', 'flex');
    var dest_width = $(cameraDivId).width();
    var dest_height = (dest_width/640)*480;
    Webcam.set({
        width: dest_width,
        height: dest_height,
        dest_width: dest_width,
        dest_height: dest_height,
        image_format: 'jpeg',
        jpeg_quality: 90
    });
    Webcam.attach( cameraDivId );
    callback = base64SnapHandlerCallback;

    $("#try_again_button").click(function() {
        reset_webcam();
    });

    Webcam.on( 'error', function(err) {
        if (err == 'Could not access webcam.') {
            $("#webcam_loading_placeholder").css('display', 'none');
            $("#webcam_not_allowed_error").css('display', 'flex');
            return;
        }
        alert(err);
    } );

    Webcam.on( 'live', function() {
        hideAllMessages();
        enableCountDownButton();
    } );

    Webcam.on( 'flash', function() {
        hideAllMessages();
    });
}

var hideAllMessages = function() {
    $(".newBoothWebcamMessageRegion").each(function(idx, div) {
        $(div).css('display', 'none');
    });
    $(".newBoothWebcamMessage").each(function(idx, div) {
        $(div).css('display', 'none');
    });
};

function reset_webcam() {
    Webcam.unfreeze();
    enableCountDownButton();
}

function take_snapshot() {
    Webcam.freeze();
    Webcam.snap( function(data_uri) {
        callback(data_uri);
    } );
    Webcam.freeze();
}

function enableCountDownButton() {
    $("#count_down_button").click(function() {
        startCountDown(3);
    });
    $("#snap_button").click(function() {
        take_snapshot();
    });
    $("#blurb_region").addClass("bottom");
    $("#finish_buttons").removeClass("bottom");
    $("#capture_buttons").css('display', 'flex');
    $("#try_again_buttons").hide();
    $("#try_again_button").attr('disabled','disabled');
    $("#post_button_placeholder").css('display', 'initial');
    $("#post_button").hide();
}

var enableAfterSnapButtons = function(onPostButtonClicked) {
    $("#blurb_region").removeClass("bottom");
    $("#finish_buttons").addClass("bottom");
    $("#capture_buttons").hide();
    $("#try_again_buttons").css('display', 'flex');
    $("#try_again_buttons").show();
    $("#try_again_button").removeAttr('disabled');
    $("#post_button_placeholder").hide();
    $("#post_button").css('display', 'block');
    $("#post_button").click(function() {
        if ("undefined" !== typeof(onPostButtonClicked) && onPostButtonClicked != null) {
            onPostButtonClicked();
        }
        $("#post_button").hide();
        $("#finish_buttons").append($("<img/>", {
            src: '{{baseUrl}}/media/ajax-loader.gif'
        }));
    });
};


//TODO: Is this used?
//function tryAgain() {
//    enableCountDownButton();
//    $(App.canvas).css('display', 'none');
//    var video = document.getElementsByTagName('video')[0];
//    $(video).show();
//    $("#post_error").slideUp();
//}

function startCountDown(count) {
    $("#webcam_count_down_1").hide();
    $("#webcam_count_down_2").hide();
    $("#webcam_count_down_3").hide();
    if (count == 3) {
        $("#webcam_count_down_3").show();
    } else if (count == 2) {
        $("#webcam_count_down_2").show();
    } else if (count == 1) {
        $("#webcam_count_down_1").show();
        $("#webcam_count_down_1").fadeOut('slow');
    } else {
        take_snapshot();
        return;
    }
    setTimeout(function() {
        startCountDown(count - 1);
    }, 1000);
}