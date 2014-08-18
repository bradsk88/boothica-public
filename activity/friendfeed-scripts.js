
var page = 1;

function openFriendFeed() {

    window.scrollTo(0, 0);
    $("#centerpane").html(
        "<div class = \"centersection\" id=\"friendfeedrecentuserssectiom\">" +
            "<div class  \"sectiontitle\">" +
            "Recently Active Users" +
            "</div>" +
            "</div>" +
            "<div class = \"centersection\" id=\"fullfriendfeed\"></div>");
    $("#friendfeedrecentuserssectiom").html("<div></div>"); //TODO: Recent users
    $("#fullfriendfeed").html("<div class = 'loadspinner'></div>");

    refreshRecentlyActiveFriends();
    loadInitialFriendFeedCells();

}

function loadMoreFriendFeed() {
    $("#friendfeedmorebutton").html("<div class = 'loadspinner'></div>");
    $.post("/_mobile/friendfeed.php", {
        pagenum: page+1,
        numperpage: 9
    }, function (data) {
        $("#friendfeedmorebutton").remove();
        $("#fullfriendfeed").append(getFriendFeedGridCellsHTML(data, page+1));
        $('html,body').animate({
            scrollTop: $("#page"+(page+1)).offset().top - 50
        });
        page = page+1;
    }, "json")
        .fail(function (jqXHR, textStatus, errorThrown) {
            $("#friendfeedmorebutton").html("There was a problem... Try Again?" +
                "<div class = 'sectionrefresh' onclick='loadMoreFriendFeed()'></div>");
        })
}

function refreshRecentlyActiveFriends() {

}

function loadInitialFriendFeedCells() {
    $.post("/_mobile/friendfeed.php", {
        pagenum: 1,
        numperpage: 9
    }, function (data) {
        $("#fullfriendfeed").html(getFriendFeedGridHTML(data, "loadInitialFriendFeedCells"));
    }, "json")
        .fail(function (jqXHR, textStatus, errorThrown) {
            $("#fullfriendfeed").html("There was a problem... [" + textStatus + ":" + errorThrown + "]" +
                "<div class = 'sectionrefresh' onclick='loadInitialFriendFeedCells()'></div>");
            console.debug(textStatus);
            console.debug(errorThrown);
        })
}

function getFriendFeedGridHTML(data, commandName) {

    var html =
        "<div class = \"narrowboothpad\"></div>" +
            "<div class = 'sectiontitle' onclick=\"openFriendFeed()\">" +
            "New Booths (from your friends)" +
            "</div>" +
            "<div class = 'sectionrefresh' onclick='" + commandName + "()'></div>" +
            "<div style = 'clear: both;'></div>";
    html = html + getFriendFeedGridCellsHTML(data, page);
    return html;
}

function getFriendFeedGridCellsHTML(data, pageNum) {
    return getFeedGridCellsHTML(data, pageNum, "friendfeedmorebutton", "loadMoreFriendFeed()");
}
