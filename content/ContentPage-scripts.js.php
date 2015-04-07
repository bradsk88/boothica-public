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

function getDataType() {
    if ("undefined" !== typeof(debugging) && debugging) {
        return "text";
    }
    return "json";
}

function myDebug(message) {
    if ("undefined" === typeof(window.console)) {
        //do nothing
        return;
    }

    if ("undefined" === typeof(window.console.log)) {
        //do nothing
        return;
    }
    console.log(message);
}

$(window).bind("load", function() {

    if ('undefined' === typeof window.username) {
        $("#rightcard").remove();
        reloadPublicFeed();
        loadRandomBooths();
        return;
    } else {
        reloadFriendFeed();
        reloadNotificationsNum();
        reloadPMCount();
        reloadBoothsCount();
    }
    reloadSiteWideNotifications();
    reloadPublicFeed();
    setTimeout(autoReloadCounts,1000*60*10);
    var _homebutton = $("#homebutton");
    _homebutton.removeAttr("href");
    _homebutton.click(function() {
        $("head").append("<script type = 'text/javascript' src='$base/activity/activity-scripts.js'></script>");
        backToMain();
    });

    $("#boothsnum").removeAttr("href");
    var _friendsnum = $("#friendsnum");
    _friendsnum.removeAttr("href");
    _friendsnum.click(function() {
        $("head").append("<script type = 'text/javascript' src='$base/friends/friends-scripts.js'></script>");
        openFriends();
    });
});

function reloadBoothsCount() {
    $.post("$base/_mobile/getboothcount.php", {
    }, function (data) {
        myDebug("Booth count response was: " + data);
        if (data == 0) {
            _notifscountwrap.hide();
            return;
        }
        var count = $("#boothsnum");
        if (isFinite(String(data))) {
            count.text(data + " Booths");
            return;
        }
        count.text("??? Booths");
        myDebug("Booth count error was: " + data);
    })
        .fail(function (jqXHR, textStatus, errorThrown) {
            $("#boothsnum").text("??? Booths");
            myDebug("Booth count error was: " + textStatus + ":" + errorThrown);
        });
}

function openSettings() {
    $("#centerpane").html(
        "<div class = 'centersection'>" +
            "<div class = 'settingsbutton' onclick='changeToDefaultAndReset()'>Back</div>" +
            "<a href = '$base/account'>" +
                "<div class = 'settingsbutton'>Account Settings</div>" +
            "</a>" +
            "<a href = '$base/info/reportform'>" +
                "<div class = 'settingsbutton'>Report a Bug</div>" +
            "</a>" +
            "<a href = '$base/info/reportform?type=feat'>" +
                "<div class = 'settingsbutton'>Request a Feature</div>" +
            "</a>" +
            "<a href = '$base/dologout'>" +
                "<div class = 'settingsbutton'>Log Out</div>" +
            "</a>" +
        "</div>");



    //this makes clicking the gear again go back to the page's default center view
    //it also makes subsequent clicks open this^ function again.
    $("#headgear").attr("onclick", "changeToDefaultAndReset()");
    window.scrollTo(0, 0);
}

function changeToDefaultAndReset() {

    $("#headgear").attr("onclick", "openSettings()");
    if ("undefined" !== typeof(window.defaultPopulateCenterFunction)) {
        window[window.defaultPopulateCenterFunction]();
    }

}


function reloadFriendFeed() {
    if ($("#primaryfeedright").is(':visible')) {
        doReloadFriendFeed("#primaryfeedright", "#leftfeed");
        return;
    }
    doReloadFriendFeed("#leftfeed", "#primaryfeedright");
}

function doReloadFriendFeed(visible, invisible) {
    $(visible).html("<div class = 'loadspinner'></div>");
    $(invisible).html("<div class = 'loadspinner'></div>");
    $.post("$base/_mobile/friendfeed.php", {
        username: window.username,
        numberofbooths: "3"
    }, function (data) {
        var h = getFriendFeedHTML(data, "reloadFriendFeed");
        $(visible).html(h);
        $(invisible).on("show", $(invisible).html(h))
    }, getDataType())
        .fail(function () {
            $(visible).html("There was a problem..." +
            "<div class = 'sectionrefresh' onclick='reloadFriendFeed()'></div>");
            $(invisible).html("There was a problem..." +
            "<div class = 'sectionrefresh' onclick='reloadFriendFeed()'></div>");
        });
}

function reloadSiteWideNotifications() {
    $("#centerpane").prepend("<div style = 'text-align: center;' class = 'centersection'>" +
    "   <a href = '$base/userpages/friendsactivity'>Click here for old layout (will be phased out soon)</a></div>");
    $.post("$base/_mobile/getsitewidenotifications.php", {
    }, function (data) {
        myDebug("about to prepend");
        $("#centerpane").prepend(getSiteWideNotifsHTML(data));
        myDebug("prepended");
    }, getDataType())
        .fail(function (jqXHR, textStatus, errorThrown) {
            $("#centerpane").prepend("Failed to load site messages" + errorThrown +
                "<div class = 'sectionrefresh' onclick='reloadSiteWideNotifications()'></div>");
        });

    //TODO: Give this its own function
    $.post("$base/_mobile/getnewfriendrequests.php", {
    }, function (data) {
        if (data <= 0) {
            return;
        }
        myDebug("about to prepend");
        $("#centerpane").prepend("<a href = '$base/userpages/friendrequests'>" +
            "<div class = 'centersection' style = 'text-align: center; background-color: #77c1ff'>" +
            "You have new friend requests" +
            "</div>" +
            "</a>");
        myDebug("prepended");
    })
        .fail(function (jqXHR, textStatus, errorThrown) {
            $("#centerpane").prepend("Failed to load mew friend requests" + errorThrown +
                "<div class = 'sectionrefresh' onclick='reloadSiteWideNotifications()'></div>");
        });
}

function reloadPublicFeed() {
    var secondaryFeed = "#rightfeed";
    $(secondaryFeed).html("<div class = 'loadspinner'></div>");
    $.post("$base/_mobile/publicfeed.php", {
        numberofbooths: "3",
        includeFriends: false,
        numperpage: 3
    }, function (data) {
        $(secondaryFeed).html(getLiveFeedHTML(data, "reloadPublicFeed", "Public Feed"));
    }, getDataType())
        .fail(function () {
            $(secondaryFeed).html("There was a problem..." +
                "<div class = 'sectionrefresh' onclick='reloadPublicFeed()'></div>");
        });
}

function loadRandomBooths() {
    var primaryFeed = "#leftfeed";
    if ($("#primaryfeedright").is(':visible')) {
        primaryFeed = "#primaryfeedright";
    }
    doLoadRandomBooths(primaryFeed);
}

function doLoadRandomBooths(primaryFeed) {
    $(primaryFeed).html("<div class = 'loadspinner'></div>");
    $.post("$base/_mobile/randompublicbooths.php", {
        numperpage: 10
    }, function (data) {
        $(primaryFeed).html(getLiveFeedHTML(data, "loadRandomBooths", "Random Booths"));
    }, getDataType())
        .fail(function () {
            $(primaryFeed).html("There was a problem..." +
            "<div class = 'sectionrefresh' onclick='loadRandomBooths()'></div>");
        });
}

function getFriendFeedHTML(data,  commandName) {
    $("head").append("<script type = 'text/javascript' src = '$base/activity/friendfeed-scripts.js'></script>");
    $("head").append("<script type = 'text/javascript' src = '$base/common/feed-scripts.js'></script>");
    if ("undefined" !== typeof(debugging) && debugging) {
        return data;
    }
    var html =
        "<div class = 'narrowboothpad'></div>" +
            "<div class = 'sectiontitle' onclick='openFriendFeed()' style = 'cursor: pointer;'>" +
            "New Booths" +
            "</div>" +
            "<div class = 'sectionrefresh' onclick='" + commandName + "()'>" +
            "</div>" +
            "<div style = 'clear: both;'></div>";
    html = html + getBoothFeedHTML(data, "openFriendFeed");
    return html;
}


function getLiveFeedHTML(data,  commandName, sectionTitle) {
    if ("undefined" !== typeof(debugging) && debugging) {
        return data;
    }
    var html =
        "<div class = 'narrowboothpad'></div>" +
            "<div class = 'sectiontitle' onclick='openLiveFeed()' style = 'cursor: pointer;'>" +
            sectionTitle +
            "</div>" +
            "<div class = 'sectionrefresh' onclick='" + commandName + "()'>" +
            "</div>" +
            "<div style = 'clear: both;'></div>";
    html = html + getBoothFeedHTML(data, "openLiveFeed");
    return html;
}


function getBoothFeedHTML(data, onClickFunc) {
    $("head").append("<script type = 'text/javascript' src = '$base/common/truncate.js'></script>");
    var html = "";
    $.each(data, function (idx, obj) {
        var bgImage = "/booths/" + obj.imageHash + "." + obj.filetype;
        var blurb = obj.blurb;
        var content = $('<div>' + blurb + '</div>');
        content.find('a').replaceWith(function() { return this.childNodes; });
        var blurb = content.html();
        blurb = truncate(blurb, 200, '');
        //TODO: The above truncate code is duplicated in feed-scripts.js/getFeedGridCellsHTML
        var cellId = "boothlink"+obj.boothnum;
        html = html +
            "<div class = 'narrowbooth'>" +
                "<div class = 'narrowboothpadline'></div>" +
                "<div class = 'narrowboothusername'>" + obj.boothername + "</div>" +
                "<div class = 'narrowboothpad'></div>" +
                "<div class = 'narrowboothcell'>" +
                    "<a id = '"+cellId+"' href = '$base/users/" + obj.boothername + "/" + obj.boothnum + "'>" +
                        "<div class = 'narrowboothaspect'></div>" +
                        "<div class = 'narrowboothimage' style = 'background-image: url($base" + bgImage + ")'></div>" +
                    "</a>" +
                "</div>" +
            "</div>" +
            "<div class = 'narrowboothtextwrapper'>" +
					"<div class = 'narrowbooth-text'>" + blurb + "</div>" +
					"<div class = 'narrowbooth-textshadow'></div>" +
			"</div>";
        $('body').on('click', "#"+cellId, function (e) {
            if ("undefined" !== typeof(e) && e.button == 0) {
                e.preventDefault();
                openBooth(obj.boothnum);
            }
        });
    });
    html = html +
        "<div class = 'narrowboothpad'></div>" +
        "<div class = 'narrowboothpadline'></div>" +
        "<div class = 'plainbutton plainbuttonright standardbutton' onclick='"+onClickFunc+"()'>More</div>";
    return html;
}


function getNotificationsHTML(data) {
    if ("undefined" !== typeof(debugging) && debugging) {
        return data;
    }
    var html = "<div class = 'sectiontitle'>Notifications</div>" +
        "<div class = 'sectionrefresh' onclick='reloadUsersNotifications()'></div>" +
        "<div style = 'clear: both;'></div>";
    $.each(data, function (idx, obj) {
        var cellId = "notification"+obj.boothnum+""+idx;
        html = html +
            "<div class = 'centersection'>" +
            "<div class = 'convoimages'>" +
            "<a id = '"+cellId+"' href = '$base/users/"+obj.boothername+"/"+obj.boothnum+"'>" +
            "<div class = 'convoaspect'></div>" +
            "<div class = 'convocommenterimage' style = 'background-image: url(" + obj.iconImage + ")'></div>" +
            "</a>" +
            "</div>" +
            "<div class = 'convo'>" +
            "<div class = 'convocommentername'>" + obj.mentioner + " commented:</div>" +
            "<div class = 'convotext'>" + obj.comment + "</div>" +
            "</div>" +
            "<div style = 'clear:both;'></div>" +
            "</div>";
        $('body').on('click', "#"+cellId, function (e) {
            if ("undefined" !== typeof(e) && e.button == 0) {
                e.preventDefault();
                openBooth(obj.boothnum);
            }
        });
    });
    return html;
}


function getSiteWideNotifsHTML(data) {
    if ("undefined" !== typeof(debugging) && debugging) {
        return "DEBUG:"+data;
    }
    var html = "<div class = 'widenotifs'>";
    $.each(data, function (idx, obj) {
        var clazz = "normalsitewide ";
        if (obj.severity == "low") {
            clazz = "";
        }
        if (obj.severity == "high") {
            clazz = "highsitewide ";
        }
        html = html +
            "<a href = '" + obj.url + "'>" +
            "<div class = '"+clazz+"centersection'>" +
            obj.message +
            "</div>" +
            "</a>";

    });
    return  html + "</div>";
}

function autoReloadCounts() {

    reloadNotificationsNum();
    reloadPMCount();
    setTimeout(autoReloadCounts,1000*60*10);
}

function reloadNotificationsNum() {
    $.post("$base/_mobile/checknotifications.php", {
    }, function (data) {
        if (window.console )
        myDebug("Notifications response was: " + data);
        var _notifscountwrap = $("#notifscountwrap");
        if (data == 0) {
            _notifscountwrap.hide();
            return;
        }
        var _notifscount = $("#notifscount");
        if (isFinite(String(data))) {
            var text = data;
            if (data > 9) {
                text = "9+";
            }
            if (data < 1) {
                _notifscountwrap.hide();
                return;
            }
            _notifscountwrap.show();
            _notifscount.text(text);
            return;
        }
        _notifscountwrap.show();
        _notifscount.text("?");
        myDebug("Notifications error was: " + data);
    })
        .fail(function (jqXHR, textStatus, errorThrown) {
            $("#notifscountwrap").show();
            $("#notifscount").text("?");
            myDebug("Notifications error was: " + textStatus + ":" + errorThrown);
        });
}

function reloadUsersNotifications() {
    $("#centerpane").prepend("<div id = 'notifsload' class = 'loadspinner'></div>");
    $.post("$base/_mobile/notifications.php", {
    }, function (data) {
        $("#centerpane").html(getNotificationsHTML(data));
    }, getDataType())
        .fail(function (jqXHR, textStatus, errorThrown) {
            $("#notifsload").html("There was a problem... [" + textStatus + "]" +
                "<div class = 'sectionrefresh' onclick='reloadUsersNotifications()'></div>");
        });
}


function reloadPMCount() {
    $.post("$base/_mobile/checkpmcount.php", {
    }, function (data) {
        myDebug("PM count response was: " + data);
        var _pmscountwrap = $("#pmscountwrap");
        if (data == 0) {
            _pmscountwrap.hide();
            return;
        }
        var _pmscount = $("#pmscount");
        if (isFinite(String(data))) {
            var text = data;
            if (data > 9) {
                text = "9+";
            }
            if (data < 1) {
                _pmscountwrap.hide();
                return;
            }
            _pmscountwrap.show();
            _pmscount.text(text);
            return;
        }
        _pmscountwrap.show();
        _pmscount.text("?");
        myDebug("PM count error was: " + data);
    })
        .fail(function (jqXHR, textStatus, errorThrown) {
            $("#pmscountwrap").show();
            $("#pmscount").text("?");
            myDebug("PM count error was: " + textStatus + ":" + errorThrown);
        });
}


function backToMain() {
    pushStateIfDifferent("activity");
    $("#centerpane").html(
        "<div class = 'centersection' id= 'newmembers'></div>" +
            "<div class = 'centersection' id='conversation'></div>");
    reloadSiteWideNotifications();
    reloadNewMembersSpot();
    reloadInteractionFeed();
}

EOT;
