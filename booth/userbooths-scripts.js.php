<?PHP

# These scripts are deprecated.  See user-booths-scripts.js.php

header('Content-Type: application/javascript');

$prependage = '';
error_reporting(0);
if (strpos(__FILE__, '_dev')) {
    $prependage = '/_dev';
    error_reporting(E_ALL);
}

require_once("{$_SERVER['DOCUMENT_ROOT']}".$prependage."/common/boiler.php");

$base = base();

echo <<<EOT
var page = 1;

function openUserFeed(username) {

    window.scrollTo(0, 0);
    $("#centerpane").html("<div class = 'centersection' id='fulluserfeed'></div>");
    $("#fulluserfeed").html("<div class = 'loadspinner'></div>");

    loadInitialUserFeedCells(username);
    pushStateIfDifferent("users/"+username);

}

function loadMoreUserFeed(username) {
    $("#userfeedmorebutton").html("<div class = 'loadspinner'></div>");
    $.post("$base/_mobile/userfeed.php", {
        boothername: username,
        pagenum: page+1,
        numperpage: 9
    }, function (data) {
        $("#userfeedmorebutton").remove();
        $("#fulluserfeed").append(getUserFeedGridCellsHTML(data, page+1, username));
        $('html,body').animate({
            scrollTop: $("#page"+(page+1)).offset().top - 50
        });
        page = page+1;
    }, "json")
        .fail(function (jqXHR, textStatus, errorThrown) {
            $("#userfeedmorebutton").html("There was a problem... Try Again?" +
                "<div class = 'sectionrefresh' onclick='loadMoreUserFeed("+username+")'></div>");
        })
}

function sendFriendRequest(username) {
    var confirm2 = confirm("Do you want to send " + username + " a friend request?");
    if (confirm2) {
        location.href = "$base/actions/request?username="+username;
    }
}


function loadInitialUserFeedCells(username, dispName) {
    $.post("$base/_mobile/userfeed.php", {
        boothername: username,
        pagenum: 1,
        numperpage: 9
    }, function (data) {
        $("#fulluserfeed").html(getUserFeedGridHTML(data, username, "loadInitialUserFeedCells"));
    }, "json")
        .fail(function (jqXHR, textStatus, errorThrown) {
            $("#fulluserfeed").html("There was a problem... [" + textStatus + ":" + errorThrown + "]" +
                "<div class = 'sectionrefresh' onclick='loadInitialUserFeedCells()'></div>");
            console.debug(textStatus);
            console.debug(errorThrown);
        })
}

function getUserFeedGridHTML(data, username, commandName) {

    var dispName = username;
    $.each(data, function (idx, obj) {
        dispName = obj.bootherdisplayname;
    });
    var html =
        "<div class = 'narrowboothpad'></div>" +
            "<div class = 'sectiontitle' onclick='openUserFeed("+username+")'>" +
            dispName + "'s Booths" +
            "</div>" +
            "<div class = 'sectionrefresh' onclick='" + commandName + "()'></div>" +
            "<div style = 'clear: both;'></div>";
    html = html + getUserFeedGridCellsHTML(data, page, username);
    return html;
}

function getUserFeedGridCellsHTML(data, pageNum, username) {
    return getFeedGridCellsHTML(data, pageNum, "userfeedmorebutton", "loadMoreUserFeed('"+username+"')");
}
EOT;
