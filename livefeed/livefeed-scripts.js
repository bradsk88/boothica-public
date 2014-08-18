
var page = 1;

function openLiveFeed() {
    $("head").append("<script type = 'text/javascript' src = '/common/feed-scripts.js'></script>");
    $("head").append("<script type = 'text/javascript' src = '/common/truncate.js'></script>");
    window.scrollTo(0, 0);
    $("#centerpane").html(
        "<div class = \"centersection\" id=\"livefeedrecentuserssectiom\">" +
            "<div class  \"sectiontitle\">" +
                "Recently Active Users" +
            "</div>" +
        "</div>" +
        "<div class = \"centersection\" id=\"fulllivefeed\"></div>");
    $("#livefeedrecentuserssectiom").html("<div></div>"); //TODO: Recent users
    $("#fulllivefeed").html("<div class = 'loadspinner'></div>");

    refreshRecentlyActiveUsers();
    loadInitialPublicFeedCells();

}

function loadMoreLiveFeed() {
    $("#livefeedmorebutton").html("<div class = 'loadspinner'></div>");
    $.post("/_mobile/publicfeed.php", {
        pagenum: page+1,
        numperpage: 9
    }, function (data) {
        $("#livefeedmorebutton").remove();
        $("#fulllivefeed").append(getLiveFeedGridCellsHTML(data, page+1));
        $('html,body').animate({
            scrollTop: $("#page"+(page+1)).offset().top - 50
        });
        page = page+1;
    }, "json")
        .fail(function (jqXHR, textStatus, errorThrown) {
            $("#livefeedmorebutton").html("There was a problem... Try Again?" +
                "<div class = 'sectionrefresh' onclick='loadMoreLiveFeed()'></div>");
        })
}

function refreshRecentlyActiveUsers() {

}

function loadInitialPublicFeedCells() {
    $.post("/_mobile/publicfeed.php", {
        pagenum: 1,
        numperpage: 9
    }, function (data) {
        $("#fulllivefeed").html(getLiveFeedGridHTML(data, "loadInitialPublicFeedCells"));
    }, "json")
        .fail(function (jqXHR, textStatus, errorThrown) {
            $("#fulllivefeed").html("There was a problem... [" + textStatus + ":" + errorThrown + "]" +
                "<div class = 'sectionrefresh' onclick='loadInitialPublicFeedCells()'></div>");
            console.debug(textStatus);
            console.debug(errorThrown);
        })
}

function getLiveFeedGridHTML(data, commandName) {

    var html =
        "<div class = \"narrowboothpad\"></div>" +
            "<div class = 'sectiontitle' onclick=\"openLiveFeed()\">" +
            "Public Feed" +
            "</div>" +
            "<div class = 'sectionrefresh' onclick='" + commandName + "()'></div>" +
            "<div style = 'clear: both;'></div>";
    html = html + getLiveFeedGridCellsHTML(data, page);
    return html;
}

function getLiveFeedGridCellsHTML(data, pageNum) {
    return getFeedGridCellsHTML(data, pageNum, "livefeedmorebutton", "loadMoreLiveFeed()");
}
