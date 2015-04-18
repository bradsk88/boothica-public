function loadOneBooth(boothnum) {
    $.post("{{baseUrl}}/_mobile/v2/getbooth.php", {
        boothnum: boothnum
    }, function (data) {
        renderOneBoothFromData(data);
    }, "json")
        .fail(function (jqXHR, textStatus, errorThrown) {
            $("#user_booth_body").html("Error while loading booth");
        })
}

var enableNavButtons = function(data) {
    if (data.success.nextnum > 0) {
        $("#next_booth_form").attr("action", "{{baseUrl}}/users/" + data.success.boothername + "/" + data.success.nextnum);
        $("#next_booth_button").prop("disabled", false);
    }
    if (data.success.prevnum > 0) {
        $("#prev_booth_form").attr("action", "{{baseUrl}}/users/" + data.success.boothername + "/" + data.success.prevnum);
        $("#prev_booth_button").prop("disabled", false);
    }
};

var renderOneBoothFromData = function (data) {
    $.get('{{baseUrl}}/booth/templates/oneBooth.mst', function (template) {
        var html = "";
        if (typeof(data.success) === "undefined") {
            html = "error: " + data.error;
        } else {
            $("#booth_buttons").css("visibility", "visible");
            enableNavButtons(data);
            html += Mustache.render(template, {
                boothImageUrl: data.success.absoluteImageUrl,
                blurb: data.success.blurb,
                boothNumber: data.success.boothnum
            });
        }
        $("#user_booth_body").html(html);
        loadOneBoothComments(data.success.boothnum);
        loadOneBoothLikes(data.success.likes, data.success.boothnum);
    });

};

var loadOneBoothComments = function (boothnum) {
    var spinner = $("<img/>", {
        src: "{{baseUrl}}/media/ajax-loader.gif",
        style: "margin: 0 auto;"
    });
    var loader = $("<div/>", {
        id: "loadmoreajaxloader",
        html: spinner
    });
    $("#user_booth_comments").append(loader);

    $.post("{{baseUrl}}/_mobile/v2/getcomments.php", {
        boothnum: boothnum
    }, function (data) {
        renderOneBoothCommentsFromData(data);
        $(spinner).hide();
    }, "json")
        .fail(function (jqXHR, textStatus, errorThrown) {
            $(loader).hide();
            $("#user_booth_comments").html("Error while loading comments");
        })
};

var renderOneBoothCommentsFromData = function (data) {
    $.get('{{baseUrl}}/framing/templates/textCommentNoContext.mst', function (template) {
        var html = "";
        if (typeof(data.success) === "undefined") {
            html = "error: " + data.error;
        } else {
            $.each(data.success, function (idx, obj) {
                html += Mustache.render(template, {
                    text: obj.commenttext,
                    username: obj.commentername,
                    displayName: obj.commenterdisplayname,
                    imageUrl: obj.absoluteIconImageUrl,
                    baseUrl: "{{baseUrl}}"
                });
            });
        }
        $("#user_booth_comments").html(html);
    });
};

var loadOneBoothLikes = function(likesNum, boothNum) {
    $.post("{{baseUrl}}/_mobile/v2/getboothlikeusers.php", {
        boothnum: boothNum
    }, function (data) {
        renderOneBoothLikesFromData(data);
    }, "json")
        .fail(function (jqXHR, textStatus, errorThrown) {
            $("#user_booth_likes").html("Error while loading comments");
        })
};

var renderOneBoothLikesFromData = function(data) {
    var html = "";
    if (typeof(data.success) === "undefined") {
        html = "error: " + data.error;
    } else {
        $.each(data.success, function (idx, obj) {

            var likesList = $("<div/>", {
                class: "likesListHorizontal"
            });

            $.each(data.success.likeusers, function (idx, obj) {
                var userImageRegion = $("<div/>", {
                    class: "likeUserImageRegion"
                });
                var userImage = $("<img/>", {
                    src: obj.userImageAbsoluteUrl
                });
                userImage.appendTo(userImageRegion);
                userImageRegion.appendTo("#user_booth_likes");
            });
        });
    }
};

function postComment(boothnum, boothername) {
    $("#post_comment_button").hide();
    $("#post_comment_button").after($("<img/>", {
        src: "{{baseUrl}}/media/ajax-loader.gif",
        id: "post_comment_spinner"
    }));
    var commentText = $("#comment_textarea").val();
    confirm = true;
    if (commentText.length == 0) {
        confirm = prompt("Are you sure you want to post an empty comment?");
    }
    if (!confirm) {
        return;
    }
    $.post("{{baseUrl}}/_mobile/v2/putcomment.php", {
            boothnum: boothnum,
            commenttext: commentText
        },
        function (data) {
            if (typeof(data.success) !== "undefined") {
                $("#post_comment_button").show();
                $("#post_comment_spinner").remove();
                loadOneBoothComments(boothnum);
                $("#comment_textarea").val("");
            } else if (typeof(data.error) !== "undefined") {
                handleError(data.error);
            } else {
                handleError("API Fail");
            }
        },
        "json")
        .fail(function () {
            handleError("API Fail");
        });
}

var handleError = function (error) {
    $("#post_comment_button").show();
    $("#post_comment_spinner").remove();
    $("#post_comment_button").after($("<span/>", {
        id: "post_comment_error",
        text: error
    }));
};

function likeBooth(boothnum, boothername) {
    $("#like_booth_button").hide();
    $("#like_booth_button").after($("<img/>", {
        src: "{{baseUrl}}/media/ajax-loader.gif",
        id: "like_booth_spinner"
    }));
}
