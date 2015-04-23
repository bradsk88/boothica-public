function postViaAPINow(imageBase64, commentText) {
    if ("undefined" === typeof(window.boothNum)) {
        alert("Unrecoverable error: no boothNum");
        return;
    }
    $.ajax({
        type: "POST",
        url: "{{baseUrl}}/_mobile/v2/putmediacomment",
        data: {
            image: imageBase64,
            commenttext: commentText,
            boothnum: window.boothNum,
            mediatype: "photo"
        },
        success: function(data) {
            if ("undefined" === typeof(data.success)) {
                showError("undefined" === typeof(data.error) ? "Unexpected Error" : data.error);
                $("#finish_buttons").show();
                $("#try_again_buttons").show();
                return;
            }
            location.href = data.success.boothUrl;
        },
        fail: function(_, o) {
            App.showError("Unexpected Error " +  o);
            $("#finish_buttons").show();
            $("#try_again_buttons").show();
        },
        dataType: "json"
    });
    $("#finish_buttons").hide();
    $("#try_again_buttons").hide();
}

function showError(message) {
    var errorDiv = $("#post_error");
    if (typeof(errorDiv) === "undefined") {
        alert(message);
        return;
    }
    errorDiv.show();
    errorDiv.text(message);
}
