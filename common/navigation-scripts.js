
var doEndsWith;
var endsWithString;

var openActivitySection = function() {
    pushStateIfDifferent("activity");
    $("#centerpane").html(
        "<div class = \"centersection\" id=\"newmembers\"></div>" +
        "<div class = \"centersection\" id=\"conversation\"></div>");
    reloadNewMembersSpot();
    reloadInteractionFeed();
};

$(function () {

    doEndsWith = function(str, suffix) {
		return str.indexOf(suffix, str.length - suffix.length) !== -1;
	}

    endsWithString = function (str, suffix) {
		var exactMatch = doEndsWith(str, suffix);
		if (exactMatch) {
			return true;
		}
		var withSlash = doEndsWith(str, suffix + "/");
		if (withSlash) {
			return true;
		}
		return doEndsWith(str, suffix + ".php");
	};

    $(window).on("popstate", function (e) {
        var pathName = window.location.pathname;
        if (endsWithString(pathName, "activity")) {
            $("head").append("<script type = 'text/javascript' src = '/activity/activity-scripts.js'></script>");
            openActivitySection();
            return;
        }
        if (typeof(openSnapNewBooth) === "function")
        {
            if (endsWithString(pathName, "newbooth/webcam")) {
                openSnapNewBooth();
                return;
            }
        }
        if (typeof(openFileNewBooth) === "function")
        {
            if (endsWithString(pathName, "newbooth/file")) {
                openFileNewBooth();
                return;
            }
        }
        var boothRegex = /.+\/users\/.+\/([0-9]+)(?:\.php)?$/g;
        var boothMatch = boothRegex.exec(window.location);
        if (boothMatch != null && "undefined" !== typeof(boothMatch) && "undefined" !== typeof(boothMatch[1]) && boothMatch.length > 1) {
            $("head").append("<script type = 'text/javascript' src = '/booth/booth-scripts.js.php'></script>");
            openBooth(boothMatch[1]);
            return;
        }

        var userRegex = /.+\/users\/(.+)(?:\/booths)?(?:\.php)?$/g;
        var userMatch = userRegex.exec(window.location);
        if (userMatch != null && "undefined" !== typeof(userMatch) && "undefined" !== typeof(userMatch[1]) && userMatch.length > 1) {
            $("head").append("<script type = 'text/javascript' src = '/booth/userbooths-scripts.js'></script>");
            openUserFeed(userMatch[1]);
        }
    });
});

function pushStateIfDifferent(relativeDir) {
    var newLocation = window.location.pathname.split( '/' )[0]+"/" + relativeDir;
//    mebug("Curloc: " + window.location.pathname + " newLoc: " + newLocation);
    if (window.location.pathname == newLocation) {
        return;
    }
    if (curLocationEndsWith(relativeDir)) {
        return;
    }
    window.history.pushState("string", "Title", newLocation);
}

function curLocationEndsWith(relativeDir) {
    return endsWithString(window.location.pathname, relativeDir);
}


