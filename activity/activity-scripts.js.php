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

$(window).bind("load", function() {
    if ('undefined' !== typeof window.username) {
//        reloadFriendshipsSpot();
        reloadNewMembersSpot();
        reloadInteractionFeed();
        setTimeout(autoReloadInteractions,1000*60*10);
    }

});


function autoReloadInteractions() {

    if (curLocationEndsWith("activity")) {
        reloadInteractionFeed();
        setTimeout(autoReloadInteractions,1000*60*10);
    }
}

function reloadInteractionFeed() {
    initialLoadConversation();
}

function initialLoadConversation() {

    $("#conversation").html("<div class = 'loadspinner'></div>");
    $.post("/_mobile/getnewconversation.php", {
        numperpage: "10"
    }, function (data) {
        var html = "<div class = 'sectiontitle'>Interactions</div>" +
            "<div class = 'sectionrefresh' onclick='reloadInteractionFeed()'></div>" +
            "<div style = 'clear: both;'></div>";
        html += getConversationHTML(data);
        html += "<div class = 'plainbuttoninverted standardbutton' id = 'loadmoreconvobutton' onclick='loadMoreConversation(1)'>" +
            "More..." +
            "</div>";
        $("#conversation").html(html);
    }, getDataType())
        .fail(function (jqXHR, textStatus) {
            $("#conversation").html("There was a problem... [" + textStatus + "]" +
                "<div class = 'sectionrefresh' onclick='reloadInteractionFeed()'></div>");
        });

}

function loadMoreConversation(page) {

    $("#conversation").append("<div class = 'loadspinner' id = 'convospinner'></div>");
    $.post("/_mobile/getnewconversation.php", {
        numperpage: "10",
        pagenum: page + 1
    }, function (data) {
        $("#loadmoreconvobutton").remove();
        $("#convospinner").remove();
        var html = getConversationHTML(data);
        html += "<div class = 'plainbuttoninverted standardbutton' id = 'loadmoreconvobutton' onclick= 'loadMoreConversation("+page+1+")'>" +
            "More..." +
            "</div>";
        $("#conversation").append(html);
    }, getDataType())
.fail(function (jqXHR, textStatus) {
            $("#conversation").append("There was a problem... [" + textStatus + "]" +
                "<div class = 'sectionrefresh' onclick='reloadInteractionFeed()'></div>");
        });
}

function reloadNewMembersSpot() {
    $("#newmembers").html("<div class = 'loadspinner'></div>");
    $.post("/_mobile/newmembers.php", {
        username: window.username,
        numperpage: "6"
    }, function (data) {
        $("#newmembers").html(getNewMembersHTML(data));
    }, getDataType())
        .fail(function (jqXHR, textStatus) {
            $("#friendships").html("There was a problem... [" + textStatus + "]" +
                "<div class = 'sectionrefresh' onclick='reloadFriendshipsSpot()'></div>");
        });
}

function getNewMembersHTML(data) {
    var html = "<div class = 'sectiontitle'>New Members</div><div class = 'sectionrefresh' onclick='reloadNewMembersSpot()'></div><div style = 'clear: both;'></div>";
    $.each(data, function (idx, obj) {
        html = html +
            "<div class = 'newmember newmember"+idx +"'>" +
                "<div class = 'newmemberimageframe' onclick= 'openBooth("+obj.boothnum+")'>" +
                    "<div class = 'newmemberimage' style = 'background-image: url($base/booths/small/" + obj.imageHash + "." + obj.filetype + ")'>" +
                    "</div>" +
                "</div>" +
            "</div>"
    });
    return html;
}

function reloadFriendshipsSpot() {
    $("#friendships").html("<div class = 'loadspinner'></div>");
    $.post("/_mobile/newfriendsoffriends.php", {
        username: window.username,
        numperpage: "2"
    }, function (data) {
        $("#friendships").html(getFriendshipsHTML(data));
    }, getDataType())
        .fail(function (jqXHR, textStatus) {
            $("#friendships").html("There was a problem... [" + textStatus + "]" +
                "<div class = 'sectionrefresh' onclick='reloadFriendshipsSpot()'></div>");
        });
}


function getFriendshipsHTML(data) {
    if (debugging) {
        return data;
    }
    $("head").append("<script type = 'text/javascript' src = '/booth/userbooths-scripts.js'></script>");
    var html = "<div class = 'sectiontitle'>New Friendships</div><div class = 'sectionrefresh' onclick='reloadFriendshipsSpot()'></div><div style = 'clear: both;'></div>";
    $.each(data, function (idx, obj) {
        html = html +
            "<div class = 'friendshiprow'>" +
            "<div class = 'friendshipleft' onclick='openUserFeed(\"" + obj.follower + "\")'><div class = 'friendshipimg' style = 'background-image: url($base" + obj.followerImg + ")'></div></div>" +
            "<div class = 'friendshipleft'><div class = 'friendshipname'>" + obj.follower + "</div></div>" +
            "<div class = 'friendshipright' onclick='openUserFeed(\"" + obj.followee + "\")'><div class = 'friendshipimg' style = 'background-image: url($base" + obj.followeeImg + ")'></div></div>" +
            "<div class = 'friendshipright'><div class = 'friendshipname'>" + obj.followee + "</div></div>" +
            "<div style = 'clear:both;'></div>" +
            "</div>"
    });
    return html;
}


function getConversationHTML(data) {
    if (debugging) {
        return data;
    }
    var html = "";
    $.each(data, function (idx, obj) {
        var cellId = "convo" + obj.commentNum;
        if (obj.hasPhoto) {
            var imageHeightInEm = obj.imageRatio * 16;
            html = html +
                "<div class = 'centersection'>" +
                "<a id = '"+cellId+"' href = '$base/users/"+obj.boothername+"/"+obj.boothnumber+"'>" +
                    "<div class = 'convoimages-photo'>" +
                        "<div class = 'convoaspect-photo'></div>" +
                        "<div class = 'convocommenterimage-photo' style = 'background-image: url($base" + obj.commentPhotoImg + "); height: "+imageHeightInEm+"em'></div>" +
                        "<div class = 'convoboothimage-photo' style = 'background-image: url($base" + obj.boothIconImg + ")'></div>" +
                    "</div>" +
                "</a>" +
                "<div class = 'convo-photo'>" +
                "<div class = 'convocommentername'><span class = 'username' onclick='openUserFeed(\""+obj.commentername+"\")'>"+obj.commenterdisplayname + "</span> commented:</div>" +
                "<div class = 'convotext'>" + obj.comment + "</div>" +
                "</div>" +
                "<div style = 'clear:both;'></div>" +
                "</div>"
        } else {
            html = html +
                "<div class = 'centersection'>" +
                "<a id = '"+cellId+"' href = '$base/users/"+obj.boothername+"/"+obj.boothnumber+"'>" +
                    "<div class = 'convoimages'>" +
                        "<div class = 'convoaspect'></div>" +
                        "<div class = 'convocommenterimage' style = 'background-image: url($base" + obj.commenterImg + ")'></div>" +
                        "<div class = 'convoboothimage' style = 'background-image: url($base" + obj.boothIconImg + ")'></div>" +
                    "</div>" +
                "</a>" +
                "<div class = 'convo'>" +
                "<div class = 'convocommentername'><span class = 'username' onclick='openUserFeed(\""+obj.commentername+"\")'>"+obj.commenterdisplayname + "</span> commented:</div>" +
                "<div class = 'convotext'>" + obj.comment + "</div>" +
                "</div>" +
                "<div style = 'clear:both;'></div>" +
                "</div>"
        }
        $('body').on('click', "#"+cellId, function (e) {
            if ("undefined" !== typeof(e) && e.button == 0) {
                e.preventDefault();
                openBooth(obj.boothnumber);
            }
        });
    });
    return html;
}

EOT;
