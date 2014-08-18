$(window).bind("load", function() {
    if ('undefined' !== typeof window.username) {
        reloadFriendshipsSpot();
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
    $("#conversation").html("<div class = 'loadspinner'></div>");
    $.post("/_mobile/getnewconversation.php", {
        numperpage: "10"
    }, function (data) {
        $("#conversation").html(getConversationHTML(data));
    }, getDataType())
        .fail(function (jqXHR, textStatus) {
            $("#conversation").html("There was a problem... [" + textStatus + "]" +
                "<div class = 'sectionrefresh' onclick='reloadInteractionFeed()'></div>");
        });
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
            "<div class = 'friendshipleft' onclick=\"openUserFeed('"+obj.follower+"')\"><div class = 'friendshipimg' style = 'background-image: url(" + obj.followerImg + ")'></div></div>" +
            "<div class = 'friendshipleft'><div class = 'friendshipname'>" + obj.follower + "</div></div>" +
            "<div class = 'friendshipright' onclick=\"openUserFeed('"+obj.followee+"')\"><div class = 'friendshipimg' style = 'background-image: url(" + obj.followeeImg + ")'></div></div>" +
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
    var html = "<div class = 'sectiontitle'>Interactions</div>" +
        "<div class = 'sectionrefresh' onclick='reloadInteractionFeed()'></div>" +
        "<div style = 'clear: both;'></div>";
    $.each(data, function (idx, obj) {
        if (obj.hasPhoto) {
            var imageHeightInEm = obj.imageRatio * 16;
            html = html +
                "<div class = 'centersection'>" +
                "<div class = 'convoimages-photo'>" +
                "<div class = 'convoaspect-photo'></div>" +
                "<div class = 'convocommenterimage-photo' style = 'background-image: url(" + obj.commentPhotoImg + "); height: "+imageHeightInEm+"em'></div>" +
                "<div class = 'convoboothimage-photo'  onclick='openBooth(" + obj.boothnumber + ")' style = 'background-image: url(" + obj.boothIconImg + ")'></div>" +
                "</div>" +
                "<div class = 'convo-photo'>" +
                "<div class = 'convocommentername'><span class = \"username\" onclick=\"openUserFeed('"+obj.commentername+"')\">"+obj.commenterdisplayname + "</span> commented:</div>" +
                "<div class = 'convotext'>" + obj.comment + "</div>" +
                "</div>" +
                "<div style = 'clear:both;'></div>" +
                "</div>"
        } else {
            html = html +
                "<div class = 'centersection'>" +
                "<div class = 'convoimages' onclick='openBooth(" + obj.boothnumber + ")'>" +
                "<div class = 'convoaspect'></div>" +
                "<div class = 'convocommenterimage' style = 'background-image: url(" + obj.commenterImg + ")'></div>" +
                "<div class = 'convoboothimage' style = 'background-image: url(" + obj.boothIconImg + ")'></div>" +
                "</div>" +
                "<div class = 'convo'>" +
                "<div class = 'convocommentername'><span class = \"username\" onclick=\"openUserFeed('"+obj.commentername+"')\">"+obj.commenterdisplayname + "</span> commented:</div>" +
                "<div class = 'convotext'>" + obj.comment + "</div>" +
                "</div>" +
                "<div style = 'clear:both;'></div>" +
                "</div>"

        }
    });
    return html;
}
