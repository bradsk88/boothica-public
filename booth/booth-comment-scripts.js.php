<?PHP

header('Content-Type: application/javascript');

$prependage = '';
error_reporting(0);
if (strpos(__FILE__, '/_dev')) {
    $prependage = '/_dev';
    error_reporting(E_ALL);
}

require_once("{$_SERVER['DOCUMENT_ROOT']}".$prependage."/common/boiler.php");

$base = base();

echo <<<EOT

function deleteComment(boothnum, commentNumber) {
    var confirmed = confirm("Are you sure you want to delete this comment?\\n(This cannot be undone)");
    if (confirmed) {
        $.post("$base/_mobile/deletecomment.php", {
            commentnum: commentNumber
        }, function (data) {
            if (data.success) {
                reloadBoothComments(boothnum);
                return;
            }
            alert("There was a problem deleting the comment. [" + data.error + "]");
            reloadBoothComments(boothnum);
        },"json")
            .fail(function (jqXHR, textStatus, errorThrown) {
                alert("There was a problem deleting the comment. [" + textStatus + "]");
            });
    }
}

var endsWith = function (str, suffix) {
    return str.indexOf(suffix, str.length - suffix.length) !== -1;
};

function likeComment(commentNumber) {
    $.get("$base/actions/likecomment.php", {
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
EOT;
