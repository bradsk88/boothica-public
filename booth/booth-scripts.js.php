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

var debugging;debugging=false;
var boother;

function getDataType() {
    if (debugging) {
        return "text";
    }
    return "json";
}

function openBooth(boothnum) {

    window.boothnumber = boothnum;
    window.scrollTo(0, 0);

    $("head").append("<link rel= 'stylesheet' href= '$base/css/commentinput.css' type= 'text/css' media= 'screen' />");
    $("head").append("<link rel= 'stylesheet' href= '$base/css/booth.css' type= 'text/css' media= 'screen' />");
    $("head").append("<script type =  'text/javascript' src = '$base/common/jquery.a-tools-1.5.2.min.js'></script>");
    $("head").append("<script type =  'text/javascript' src = '$base/common/jquery.asuggest.js'></script>");
    $("head").append("<script type =  'text/javascript' src = 'http://ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js'></script>");
    $("#centerpane").html(
        "<div class = 'centersection' id='boothtop'></div>" +
            "<div class = 'centersection' id= 'boothcomments'></div>" +
            "<iframe class = 'centersection' id= 'comment_target' name = 'comment_target' src = '$base/comment/target.html'  style= 'width:100%;height:0;border:0 solid #FFF;'></iframe>" +
            "<div class = 'centersection' id='boothcommentinput'>");
    $("#boothtop").html("<div class = 'loadspinner'></div>");
    $("#boothcomments").html("<div class = 'loadspinner'></div>");

    reloadBoothBody(boothnum, function () {
        reloadBoothCommentInput(boothnum, function () {
            reloadBoothComments(boothnum);
        });
    });
}

var endsWith = function (str, suffix) {
    return str.indexOf(suffix, str.length - suffix.length) !== -1;
};

function likeBooth(boothnum) {
    $.post("$base/_mobile/likebooth.php", {
        boothnum: boothnum
    }, function (data) {
        if (endsWith(data, ":OK")) {
            var split = data.split(":");
            var newCount = split[0];
            $("#boothlikewrap").css("display", "inherit");
            $("#boothlike").text(newCount);
            $( "#bootheffect").fadeIn( "fast ", function () {
                $( "#bootheffect").fadeOut({
                    duration: 1500,
                    easing: "linear"
                });
            });
            return;
        }
        alert("There was a problem liking the booth. [ErrorCode:" + data + "]");
    })
        .fail(function (jqXHR, textStatus) {
            alert("There was a problem liking the booth. [" + textStatus + "]");
        });
}

function startBoothEdit(boothnum) {
    $.post("$base/_mobile/getbooth.php", {
        boothnum: boothnum
    }, function (data) {
        $("#boothblurb").remove();
        var boothtop = $("#boothtop");
        boothtop.append(getBlurbEditHTML(data));
        boothtop.css("position", "relative");
    }, getDataType())
        .fail(function (jqXHR, textStatus, errorThrown) {
            $("#boothblurb").append(
                "<div class = 'centersection'>" +
                    "There was a problem... [" + textStatus + "]" +
                    "<div class = 'sectionrefresh' onclick= 'reloadBoothBody(" + boothnum + ")'></div>" +
                    "</div>");
        });

}
function getBlurbEditHTML(data) {

	var html = "";
    $.each(data, function (idx, obj) {
		var blu = obj.blurb.replace("/<br ?\/?>/g", "\\n")
        html = html +
            "<textarea class = 'blurbedit' id = 'blurbedit'>" +
                blu +
            "</textarea>" +
            "<div id = 'changeblurbbutton' class = 'plainbutton plainbuttonright tallbutton' onclick= 'commitEdit("+obj.boothnum+")'>" +
                "Submit Changes" +
            "</div>" +
            "<div class = 'wideboothpad'></div>"
    });
    return html;
}

function commitEdit(boothnum) {
    $("#boothcommentinput").html("<div class = 'loadspinner'></div>");
    var newBlurb = $("#blurbedit").val();
    $.post("$base/_mobile/changeblurb.php", {
        boothnum: boothnum,
        blurb: newBlurb
    }, function (data) {
        $.each(data, function (idx, obj) {
            if ("undefined" !== typeof(obj.error)) {
                alert("Error: " + obj.error);
                return;
            }
            if (idx == "error") {
                alert("Error: " + obj);
                return;
            }
            reloadBoothBody(boothnum)
        });
    },"json")
        .fail(function (jqXHR, textStatus, errorThrown) {
            $("#boothcommentinput").html("There was a problem... [" + textStatus + ":" + errorThrown + "]" +
                "<div class = 'sectionrefresh' onclick= 'reloadBoothCommentInput(" + boothnum + ")'></div>");
        })
}

function reloadBoothCommentInput(boothnum, thenDo) {
//    console.debug("Opening comment input section for booth " + boothnum);
    $.post("$base/booth/getboothcommentinputsection.php", {
        boothnum: boothnum
    }, function (data) {
        $("#boothcommentinput").html(data);
        if ("undefined" !== typeof(thenDo)) {
            thenDo();
        }
    })
        .fail(function (jqXHR, textStatus, errorThrown) {
            $("#boothcommentinput").html("There was a problem... [" + textStatus + ":" + errorThrown + "]" +
                "<div class = 'sectionrefresh' onclick= 'reloadBoothCommentInput(" + boothnum + ")'></div>");
        })
}


function reloadBoothBody(boothnum, thenDo) {
    $.post("$base/_mobile/getbooth.php", {
        boothnum: boothnum
    }, function (data) {
        var boothtop = $("#boothtop");
        boothtop.html(getBoothHTML(data));
        boothtop.css("position", "relative");
        if ("undefined" === typeof(thenDo)) {
            return;
        }
        thenDo(boother);
    }, getDataType())
        .fail(function (jqXHR, textStatus, errorThrown) {
            $("#boothtop").html("There was a problem... [" + textStatus + "]" +
                "<div class = 'sectionrefresh' onclick= 'reloadBoothBody(" + boothnum + ")'></div>");
        });
}

function getSuggests(data) {
    var suggests = [];
    $.each(data, function (idx, obj) {
        var index = suggests.indexOf(obj.commentername);
        if (index === -1) {
            var toPush = "@" + obj.commentername;
            suggests.push(toPush);
        }
    });
    var arrayUnique = function (a) {
        return a.reduce(function (p, c) {
            if (p.indexOf(c) < 0) p.push(c);
            return p;
        }, []);
    };
    var out = arrayUnique(suggests);
//    console.debug("Prepared suggestions: " + out);
    return out;
}

function reloadBoothComments(boothnum) {
    $.post("$base/_mobile/getcomments.php", {
        boothnum: boothnum
    }, function (data) {
        var suggests = getSuggests(data);
        $("#commentarea").asuggest(suggests, {
             "minChunkSize ": 1,
             "delimiters":  " \\n ",
             "autoComplete": true,
             "cycleOnTab": true,
             "ignoreCase": true });
        $("#boothcomments").html(getBoothCommentsHTML(boothnum, data));
    }, getDataType())
        .fail(function (jqXHR, textStatus, errorThrown) {
            $("#boothcomments").html("There was a problem... [" + textStatus + ":" + errorThrown + "]" +
                "<div class = 'sectionrefresh' onclick= 'reloadBoothComments(" + boothnum + ")'></div>");
        });
}


function getBoothHTML(data) {
    if (debugging) {
        return data;
    }
    var html = "";
    $.each(data, function (idx, obj) {
        boother = obj.boothername;
        var newLocation = "$base/users/" + obj.boothername + "/" + obj.boothnum;
//        console.debug("Curloc: " + window.location.pathname + " newLoc: " + newLocation);
        if (window.location.pathname != newLocation) {
            window.history.pushState("string", "Title", newLocation);
        }
        html =
            "<div style = 'float: left'>" +
                "<a onclick= 'backToMain()'>" +
                "Go Back" +
                "</a>" +
                "</div>" +
                "<div style = 'position: absolute; right: 0;' width: 100px;>" +
                "<a onclick = 'openPMsForUser(\"" + obj.boothername + "\")'>" +
                "Send PM" +
                "</a>" +
                "</div>" +
                "<div class = 'navbuttons'>";
        if ("undefined" === typeof(obj.prevnum) || obj.prevnum == null) {
            html = html +
                "<div class = 'navbuttonleft plainbutton standardbutton' style = 'cursor:default'>&nbsp;</div>" +
                "<div class = 'navbutton plainbutton standardbutton' style = 'cursor:default'>&nbsp;</div>";
        } else {
            html = html +
                "<div class = 'navbuttonleft plainbutton standardbutton' onclick='openBooth(" + obj.firstnum + ")'>Oldest</div>" +
                "<div class = 'navbutton plainbutton standardbutton' onclick='openBooth(" + obj.prevnum + ")'>Prev</div>";
        }
        html = html +
            "<div class = 'navbuttoncenter plainbutton standardbutton' onclick='openUserFeed(\"" + obj.boothername + "\")'>" + obj.bootherdisplayname + "</div>";
        if ("undefined" === typeof(obj.nextnum) || obj.nextnum == null) {
            html = html +
                "<div class = 'navbutton plainbutton standardbutton' style = 'cursor:default'>&nbsp;</div>" +
                "<div class = 'navbuttonright plainbutton standardbutton' style = 'cursor:default'>&nbsp;</div>";
        } else {
            html = html +
                "<div class = 'navbutton plainbutton standardbutton' onclick='openBooth(" + obj.nextnum + ")'>Next</div>" +
                "<div class = 'navbuttonright plainbutton standardbutton' onclick='openBooth(" + obj.lastnum + ")'>Newest</div>";
        }

        var cellClass = "wideboothcell";
        if (obj.imageProp > 1.00) {
            cellClass = "wideboothcell-tall";
        }
        html = html +
            "<div style = 'clear: both'></div>" +
            "</div>" +
            "<div class = 'sectionrefresh' onclick= 'openBooth(" + obj.boothnum + ")'></div>" +
            "<div style = 'clear: both;'></div>" +
            "<div class = 'widebooth'>" +
            "<div class = '"+cellClass+"'>" +
            "<div class = 'wideboothdatestamp'>" + obj.datetime + "-UTC</div>"+ //TODO: use user "s zone
            "<img class = 'wideboothimg' src = '" + obj.imagePath + "' style =  'width: 100%'>" +
            "</div>" +
            "<div class = 'wideboothbuttons plainbutton standardbutton'>" +
            "<div class = 'wideboothbuttonshalf wideboothbuttonslefthalf'>" +
            "Booth " + obj.userboothcount;
        if (obj.userboothcount != obj.userboothnum) {
            var discrepencyMsg = obj.bootherdisplayname + " has actually posted " + obj.userboothnum + " booths, but they have deleted some.";
            html = html +
                "<span title = '" + discrepencyMsg + "'>" +
                " * " +
                "</span>";
        }
        var style = "display: none;";
        if ("undefined" !== typeof(obj.likes) && obj.likes != null && obj.likes > 0) {
            style = "display: inherited;";
        }
        html = html +
            "<div class = 'bootheffect' id = 'bootheffect'></div>" +
            "<div class = 'boothlikeslabel'>Likes:</div>" +
            "<div class = 'boothlikewrap' id = 'boothlikewrap' style = '" + style + "'>" +
            "<div class = 'boothlike' id = 'boothlike' title = '" + obj.likes + " Likes'>" +
            obj.likes +
            "</div>" +
            "</div>" +
            "</div>" +
            "<div class = 'wideboothbuttonshalf'>" +
            "<div class = 'boothbutton' onclick= 'likeBooth(" + obj.boothnum + ")'>Like</div>";
        if (window.username == obj.boothername) {
            html = html +
                "<div class = 'boothbutton' onclick= 'startBoothEdit(" + obj.boothnum + ")'>Edit</div>" +
                //TODO: Use AJAX for booth delete
                "<a href = '$base/actions/deletebooth?number=" + obj.boothnum + "'><div class = ''boothbutton'>Delete</div></a>";
        }
        html = html +
            "<div style = 'clear: both'></div>" +
            "</div>" +
            "<div style = 'clear: both'></div>" +
            "</div>" +
            "<div class = 'wideboothtext' id = 'boothblurb'>" + obj.blurb + "</div>" +
            "</div>";
        if (!obj.isfriend) {
            html = html + "<div class = 'plainbutton tallbutton' onclick='sendFriendRequest(\"" + obj.boothername + "\")'>Send Friend Request</div>";
        }
    });
    return html;
}

function getBoothCommentsHTML(boothnum, data) {
    if (debugging) {
        return data;
    }
    $("head").append("<script type = 'text/javascript' src = '$base/booth/userbooths-scripts.js'></script>");
    var html = "<div class = 'boothcomments'>";
    $.each(data, function (idx, obj) {

        var hasImage = false;
        if (obj.imageHash != null) {
            hasImage = true;
        }
        var boothcommenttextclass = "boothcommenttext";


        var imageHeightInEM = 0;
        var sectionHeight = "";
        if (hasImage) {
            imageHeightInEM = obj.imageRatio * 16;
            boothcommenttextclass = "boothcommenttextwithimage";
            sectionHeight = "min-height: " + (imageHeightInEM + 2) + "em;";
        }


        html = html +
            "<div class = 'centersection' style = 'position: relative; " + sectionHeight + "'>" +
            "<div class = '" + boothcommenttextclass + "'>" +
            "<div class = 'boothcommenttime'>" + obj.time + "</div>" +
            "<div class = 'boothcommenttextbg'>" +
            obj.commenttext +
            "</div>" +
            "</div>";
        if (hasImage) {
            html = html +
                "<div class = 'boothcommenterimageuploaded' style = 'background-image: url($base" + obj.imageHash + "); height: " + imageHeightInEM + "em;'></div>";
        } else {
            html = html +
                "<div class = 'boothcommenterimage' onclick='openUserFeed(\"" + obj.commentername + "\")' style = 'background-image: url($base" + obj.iconImage + ")'></div>";
        }
        var style = "display: none;";
        if ("undefined" !== typeof(obj.likes) && obj.likes != null && obj.likes > 0) {
            style = "display: inherited;";
        }
        var effectClass = "boothcommenterlikeseffect";
        var likeClass = "boothcommenterlikes";
        if (hasImage) {
            effectClass = "boothcommenterlikeseffectwithupload";
            likeClass = "boothcommenterlikeswithupload";
        }
        html = html +
            "<div class = '" + effectClass + "' id = 'effect" + obj.commentnum + "'></div>" +
            "<div class = '" + likeClass + "' id = 'likewrap" + obj.commentnum + "' style = '" + style + "'>" +
            "<div class = 'boothcommentlike' id = 'like" + obj.commentnum + "' title = '" + obj.likes + " Likes'>" +
            obj.likes +
            "</div>" +
            "</div>" +
            "<div class = 'boothcommentername' onclick='openUserFeed(\"" + obj.commentername + "\")'>" + obj.commenterdisplayname + "</div>" +

            "<div class = 'boothcommentbuttons'>";
        if ("undefined" !== typeof(window.username) && window.username.toLowerCase() == boother.toLowerCase() && obj.commentername != window.username) {
            html = html + "<div class = 'boothcommentbutton secondcommentbutton' onclick= 'deleteComment(" + boothnum + ", " + obj.commentnum + ")'>Delete</div>";
        } else if (obj.commentername == window.username) {
            html = html + "<div class = 'boothcommentbutton' onclick= 'deleteComment(" + boothnum + ", " + obj.commentnum + ")'>Delete</div>";
        }
        if (obj.commentername != window.username) {
            html = html + "<div class = 'boothcommentlikebutton' onclick= 'likeComment(" + obj.commentnum + ")'>Like</div>";
        }
        html = html +
            "</div>" +
            "</div>"
    });
    return  html + "</div>";
}

function target_use_complete() {
    reset_comment_area(true);
    reloadBoothComments(window.boothnumber);
    $( "#comment_target").delay(4000).animate({ height: 0 }, 600);
}

function reset_comment_area(clear) {
    reloadBoothCommentInput(window.boothnumber);
}

EOT;
