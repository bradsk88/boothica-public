function postViaAPINow(imageBase64, commentText) {
    if ("undefined" === typeof(window.boothNum)) {
        alert("Unrecoverable error: no boothNum");
        return;
    }
    $.post("{{baseUrl}}/_mobile/v2/putmediacomment", {
        image: imageBase64,
        commenttext: commentText,
        boothnum: window.boothNum,
        mediatype: "photo"
    }, function(data) {
        if ("undefined" === typeof(data.success)) {
            showError("undefined" === typeof(data.error) ? "Unexpected Error" : data.error);
            $("#finish_buttons").show();
            $("#try_again_buttons").show();
            return;
        }
        location.href = data.success.boothUrl;
    }, "json").fail(function(_, o){
        showError("Unexpected Error " +  o);
        $("#finish_buttons").show();
        $("#try_again_buttons").show();
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
