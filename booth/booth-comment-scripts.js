/**
 * Created by Brad on 6/19/14.
 */

function deleteComment(boothnum, commentNumber) {
    var confirmed = confirm("Are you sure you want to delete this comment?\n(This cannot be undone)");
    if (confirmed) {
        $.post("/_mobile/deletecomment.php", {
            commentnum: commentNumber
        }, function (data) {
            if (data == "0") {
                reloadBoothComments(boothnum);
                return;
            }
            alert("There was a problem deleting the comment. [ErrorCode:" + data + "]");
            reloadBoothComments(boothnum);
        })
            .fail(function (jqXHR, textStatus, errorThrown) {
                alert("There was a problem deleting the comment. [" + textStatus + "]");
            });
    }
}

var endsWith = function (str, suffix) {
    return str.indexOf(suffix, str.length - suffix.length) !== -1;
};

function likeComment(commentNumber) {
    $.get("/actions/likecomment.php", {
        commentnumber: commentNumber
    }, function (data) {
        if (endsWith(data, ":OK")) {
            var split = data.split(":");
            var newCount = split[0];
            $("#likewrap"+commentNumber).css("display", "inherit");
            $("#like"+commentNumber).text(newCount);
            $('#effect'+commentNumber).fadeIn('fast', function() {
                $('#effect'+commentNumber).fadeOut({
                    duration: 1500,
                    easing: "linear"
                });});
            return;
        }
        alert("There was a problem liking the comment. [ErrorCode:" + data + "]");
    })
        .fail(function (jqXHR, textStatus, errorThrown) {
            alert("There was a problem liking the comment. [" + textStatus + "]");
        });
}