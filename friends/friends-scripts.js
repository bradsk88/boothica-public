/**
 * Created by Brad on 7/3/14.
 */


var page = 1;

function openFriends() {

    window.scrollTo(0, 0);
    $("#centerpane").html(
        "<div class = \"centersection\" id=\"Friendsrecentuserssectiom\">" +
            "<div class  \"sectiontitle\">" +
            "Recently Active Users" +
            "</div>" +
            "</div>" +
            "<div class = \"centersection\" id=\"fullFriends\"></div>");
    $("#Friendsrecentuserssectiom").html("<div></div>"); //TODO: Recent users
    $("#fullFriends").html("<div class = 'loadspinner'></div>");

    refreshRecentlyActiveUsers();
    loadInitialFriendsCells();

}

function loadMoreFriends() {
    $("#Friendsmorebutton").html("<div class = 'loadspinner'></div>");
    $.post("/_mobile/friendlist.php", {
        pagenum: page+1,
        numperpage: 9
    }, function (data) {
        $("#Friendsmorebutton").remove();
        $("#fullFriends").append(getFriendsGridHTML(data, page+1));
        $('html,body').animate({
            scrollTop: $("#page"+(page+1)).offset().top - 50
        });
        page = page+1;
    }, "json")
        .fail(function (jqXHR, textStatus, errorThrown) {
            $("#Friendsmorebutton").html("There was a problem... Try Again?" +
                "<div class = 'sectionrefresh' onclick='loadMoreFriends()'></div>");
        })
}

function refreshRecentlyActiveUsers() {

}

function loadInitialFriendsCells() {
    $.post("/_mobile/friendlist.php", {
        pagenum: 1,
        numperpage: 9
    }, function (data) {
        $("#fullFriends").html(getFriendsGridHTML(data, 1));
    }, "json")
        .fail(function (jqXHR, textStatus, errorThrown) {
            $("#fullFriends").html("There was a problem... [" + textStatus + ":" + errorThrown + "]" +
                "<div class = 'sectionrefresh' onclick='loadInitialFriendsCells()'></div>");
            console.debug(textStatus);
            console.debug(errorThrown);
        })
}

function getFriendsGridHTML(data, pageNum) {
    var html = "<div class = \"gridcontainer\" id = \"page"+pageNum+"\">";
    $.each(data, function (idx, obj) {
        if (idx == "error") {
            html = "Error:" + obj;
            return;
        }
        var bgImage = "/booths/" + obj.imageHash + "." + obj.filetype;
        var blurb = obj.blurb;
        var content = $('<div>' + blurb + '</div>');
        content.find('a').replaceWith(function() { return this.childNodes; });
        var blurb = content.html();
        blurb = truncate(blurb, 200, '');
        var iHTML =
            "<div class = \"narrowboothgridwrapper\">" +
                "<div class = \"narrowbooth\" onclick=\"openUserFeed('" + obj.username + "')\">" +
                "<div class = \"narrowboothpadline\"></div>" +
                "<div class = \"narrowboothusername\">" + obj.displayname + "</div>" +
                "<div class = \"narrowboothpad\"></div>" +
                "<div class = \"narrowboothcell\">" +
                "<div class = \"narrowboothaspect\"></div>" +
                "<div class = \"narrowboothimage\" style = \"background-image: url(" + obj.iconImage + ")\"></div>" +
                "</div>" +
                "</div>" +
                "</div>";
        html = html + iHTML;
    });
    html = html +
        "<div style = 'clear: both;'></div>"+
        "</div>" +
        "<div class = \"plainbuttoninverted\" id = \"Friendsmorebutton\" onclick=\"loadMoreFriends()\">" +
        "More..." +
        "</div>";
    return html;
}

