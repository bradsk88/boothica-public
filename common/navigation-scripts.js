$(function () {
    $(window).on("popstate", function (e) {
        var pathName = window.location.pathname;
        if (endsWith(pathName, "activity")) {
            backToMain();
            return;
        }
        if (typeof(openSnapNewBooth) === "function")
        {
            if (endsWith(pathName, "newbooth/webcam")) {
                openSnapNewBooth();
                return;
            }
        }
        if (typeof(openFileNewBooth) === "function")
        {
            if (endsWith(pathName, "newbooth/file")) {
                openFileNewBooth();
                return;
            }
        }
        var boothRegex = /.+\/users\/.+\/([0-9]+)(?:\.php)?$/g;
        var boothMatch = boothRegex.exec(window.location);
        if (boothMatch != null && "undefined" !== typeof(boothMatch) && "undefined" !== typeof(boothMatch[1]) && boothMatch.length > 1) {
            $("head").append("<script type = 'text/javascript' src = '/booth/booth-scripts.js'></script>");
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
    return endsWith(window.location.pathname, relativeDir);
}

var doEndsWith = function(str, suffix) {
    return str.indexOf(suffix, str.length - suffix.length) !== -1;
}
var endsWith = function (str, suffix) {
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
