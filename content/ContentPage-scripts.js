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
    var $homebutton = $("#homebutton");
    $homebutton.removeAttr("href");
    $homebutton.click(function() {
        $("head").append("<script type = \"text/javascript\" src=\"/activity/activity-scripts.js\"></script>");
        backToMain();
    });

    $("#boothsnum").removeAttr("href");
    var $friendsnum = $("#friendsnum");
    $friendsnum.removeAttr("href");
    $friendsnum.click(function() {
        $("head").append("<script type = \"text/javascript\" src=\"/friends/friends-scripts.js\"></script>");
        openFriends();
    });
});

function reloadBoothsCount() {
    $.post("/_mobile/getboothcount.php", {
    }, function (data) {
        myDebug("Booth count response was: " + data);
        if (data == 0) {
            $notifscountwrap.hide();
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
            "<a href = '/account'>" +
                "<div class = 'settingsbutton'>Account Settings</div>" +
            "</a>" +
            "<a href = '/info/reportform'>" +
                "<div class = 'settingsbutton'>Report a Bug</div>" +
            "</a>" +
            "<a href = '/info/reportform?type=feat'>" +
                "<div class = 'settingsbutton'>Request a Feature</div>" +
            "</a>" +
            "<a href = '/dologout'>" +
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
    $("#leftfeed").html("<div class = 'loadspinner'></div>");
    $.post("/_mobile/friendfeed.php", {
        username: window.username,
        numberofbooths: "3"
    }, function (data) {
        $("#leftfeed").html(getFriendFeedHTML(data, "reloadFriendFeed"));
    }, getDataType())
        .fail(function () {
            $("#leftfeed").html("There was a problem..." +
                "<div class = 'sectionrefresh' onclick='reloadFriendFeed()'></div>");
        });
}

function reloadSiteWideNotifications() {
    $("#centerpane").prepend("<div style = \"text-align: center;\" class = \"centersection\"><a href = '/userpages/friendsactivity'>Having trouble?  Click here for the old layout</a></div>");
    $.post("/_mobile/getsitewidenotifications.php", {
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
    $.post("/_mobile/getnewfriendrequests.php", {
    }, function (data) {
        if (data <= 0) {
            return;
        }
        myDebug("about to prepend");
        $("#centerpane").prepend("<a href = '/userpages/friendrequests'>" +
            "<div class = 'centersection' style = \"text-align: center; background-color: #77c1ff\">" +
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
    $("#rightfeed").html("<div class = 'loadspinner'></div>");
    $.post("/_mobile/publicfeed.php", {
        numberofbooths: "3",
        includeFriends: false,
        numperpage: 3
    }, function (data) {
        $("#rightfeed").html(getLiveFeedHTML(data, "reloadPublicFeed", "Public Feed"));
    }, getDataType())
        .fail(function () {
            $("#rightfeed").html("There was a problem..." +
                "<div class = 'sectionrefresh' onclick='reloadPublicFeed()'></div>");
        });
}

function loadRandomBooths() {
    $("#leftfeed").html("<div class = 'loadspinner'></div>");
    $.post("/_mobile/randompublicbooths.php", {
		numperpage: 10
    }, function (data) {
        $("#leftfeed").html(getLiveFeedHTML(data, "loadRandomBooths", "Random Booths"));
    }, getDataType())
        .fail(function () {
            $("#leftfeed").html("There was a problem..." +
                "<div class = 'sectionrefresh' onclick='loadRandomBooths()'></div>");
        });
}

function getFriendFeedHTML(data,  commandName) {
    $("head").append("<script type = 'text/javascript' src = '/activity/friendfeed-scripts.js'></script>");
    $("head").append("<script type = 'text/javascript' src = '/common/feed-scripts.js'></script>");
    if ("undefined" !== typeof(debugging) && debugging) {
        return data;
    }
    var html =
        "<div class = \"narrowboothpad\"></div>" +
            "<div class = 'sectiontitle' onclick='openFriendFeed()' style = \"cursor: pointer;\">" +
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
        "<div class = \"narrowboothpad\"></div>" +
            "<div class = 'sectiontitle' onclick=\"openLiveFeed()\" style = \"cursor: pointer;\">" +
            sectionTitle +
            "</div>" +
            "<div class = 'sectionrefresh' onclick='" + commandName + "()'>" +
            "</div>" +
            "<div style = 'clear: both;'></div>";
    html = html + getBoothFeedHTML(data, "openLiveFeed");
    return html;
}


function getBoothFeedHTML(data, onClickFunc) {
    $("head").append("<script type = 'text/javascript' src = '/common/truncate.js'></script>");
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
            "<div class = \"narrowbooth\">" +
                "<div class = \"narrowboothpadline\"></div>" +
                "<div class = \"narrowboothusername\">" + obj.boothername + "</div>" +
                "<div class = \"narrowboothpad\"></div>" +
                "<div class = \"narrowboothcell\">" +
                    "<a id = \""+cellId+"\" href = \"/users/" + obj.boothername + "/" + obj.boothnum + "\">" +
                        "<div class = \"narrowboothaspect\"></div>" +
                        "<div class = \"narrowboothimage\" style = \"background-image: url(" + bgImage + ")\"></div>" +
                    "</a>" +
                "</div>" +
            "</div>" +
            "<div class = \"narrowboothtextwrapper\">" +
					"<div class = \"narrowbooth-text\">" + blurb + "</div>" +
					"<div class = \"narrowbooth-textshadow\"></div>" +
			"</div>";
        $('body').on('click', "#"+cellId, function (e) {
            if ("undefined" !== typeof(e) && e.button == 0) {
                e.preventDefault();
                openBooth(obj.boothnum);
            }
        });
    });
    html = html +
        "<div class = \"narrowboothpad\"></div>" +
        "<div class = \"narrowboothpadline\"></div>" +
        "<div class = \"plainbutton plainbuttonright standardbutton\" onclick=\""+onClickFunc+"()\">More</div>";
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
            "<a id = \""+cellId+"\" href = \"/users/"+obj.boothername+"/"+obj.boothnum+"\">" +
            "<div class = 'convoaspect'></div>" +
            "<div class = 'convocommenterimage' style = 'background-image: url(" + obj.iconImage + ")'></div>" +
            "</a>" +
            "</div>" +
            "<div class = 'convo'>" +
            "<div class = 'convocommentername'>" + obj.mentioner + " commented:</div>" +
            "<div class = 'convotext'>" + obj.comment + "</div>" +
            "</div>" +
            "<div style = 'clear:both;'></div>" +
            "</div>"
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
    $.post("/_mobile/checknotifications.php", {
    }, function (data) {
        if (window.console )
        myDebug("Notifications response was: " + data);
        var $notifscountwrap = $("#notifscountwrap");
        if (data == 0) {
            $notifscountwrap.hide();
            return;
        }
        var $notifscount = $("#notifscount");
        if (isFinite(String(data))) {
            var text = data;
            if (data > 9) {
                text = "9+";
            }
            if (data < 1) {
                $notifscountwrap.hide();
                return;
            }
            $notifscountwrap.show();
            $notifscount.text(text);
            return;
        }
        $notifscountwrap.show();
        $notifscount.text("?");
        myDebug("Notifications error was: " + data);
    })
        .fail(function (jqXHR, textStatus, errorThrown) {
            $("#notifscountwrap").show();
            $("#notifscount").text("?");
            myDebug("Notifications error was: " + textStatus + ":" + errorThrown);
        });
}

function reloadUsersNotifications() {
    $("#centerpane").prepend("<div id = \"notifsload\" class = 'loadspinner'></div>");
    $.post("/_mobile/notifications.php", {
    }, function (data) {
        $("#centerpane").html(getNotificationsHTML(data));
    }, getDataType())
        .fail(function (jqXHR, textStatus, errorThrown) {
            $("#notifsload").html("There was a problem... [" + textStatus + "]" +
                "<div class = 'sectionrefresh' onclick='reloadUsersNotifications()'></div>");
        });
}


function reloadPMCount() {
    $.post("/_mobile/checkpmcount.php", {
    }, function (data) {
        myDebug("PM count response was: " + data);
        var $pmscountwrap = $("#pmscountwrap");
        if (data == 0) {
            $pmscountwrap.hide();
            return;
        }
        var $pmscount = $("#pmscount");
        if (isFinite(String(data))) {
            var text = data;
            if (data > 9) {
                text = "9+";
            }
            if (data < 1) {
                $pmscountwrap.hide();
                return;
            }
            $pmscountwrap.show();
            $pmscount.text(text);
            return;
        }
        $pmscountwrap.show();
        $pmscount.text("?");
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
        "<div class = \"centersection\" id=\"newmembers\"></div>" +
            "<div class = \"centersection\" id=\"conversation\"></div>");
    reloadSiteWideNotifications();
    reloadNewMembersSpot();
    reloadInteractionFeed();
}
