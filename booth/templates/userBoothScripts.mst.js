var baseUrl = "{{baseUrl}}";

function loadBootherConsole(username) {

    $.post(baseUrl + "/_mobile/vBeta/getuser.php", {
        boothername: username
    }, function (data) {
        if (data.success) {
            $.get(baseUrl + '/framing/templates/bootherConsole.mst', function(template) {
                html = Mustache.render(template, {
                    baseUrl: baseUrl,
                    username: data.apiUsername,
                    bootherName: username,
                    bootherDisplayPhoto: data.success.displayPhotoAbsoluteUrl,
                    bootherDisplayName: data.success.displayName,
                    bootherPluralName: data.success.pluralDisplayName
                });
                $("#user_booths_feed").prepend(html);
            });
        } else if (data.error) {
            $("#user_booths_feed").prepend("Error: " + data.error);
        }
    }, "json");

}

//declare event to run when div is visible
function loadUserBooths(username){
    $.post(baseUrl + "/_mobile/v2/userfeed.php", {
        boothername: username,
        pagenum: 1,
        numperpage: 9
    }, function (data) {
        renderBoothsFromData(data);
    }, "json");
}

var renderBoothsFromData = function(data) {
    $.get(baseUrl + '/framing/templates/centerBooth.mst', function(template) {
        var html = "";
        if (typeof(data.success) === "undefined") {
            html = "error: " + data.error;
            return;
        }
        $.each(data.success.booths, function (idx, obj) {
            html += Mustache.render(template, {
                thumbnail: obj.absoluteImageUrlThumbnail,
                blurb: decodeURI(obj.blurb),
                boothUrl: baseUrl + "/users/" + obj.boothername + "/" + obj.boothnum,
                boothNumber: obj.boothnum
            });
            loadCommentCounts(obj.boothnum);
        });
        $("#user_booths_feed").append(html);
    });
};

var loadCommentCounts = function(boothnum) {
    $.post(baseUrl + "/_mobile/v2/getcommentcount.php", {
        boothnum: boothnum
    }, function (data) {
        if (data.success) {
            var text = data.success.count + " comments...";
            if (data.success.count == 1) {
                text = data.success.count + " comment...";
            }
            $("#centerBoothOpenButtonText"+boothnum).text(text);
        }
    }, "json");
};


var page = 1; //

function enableInfiniteScroll(username) {

    var pauseScroll = false;
    $(window).scroll(function()
    {

        if (pauseScroll) {
            return;
        }

        if ($("#body_load_more_button").is(":visible")) { // Load more button is only visible on small displays
            return;
        }

        if($(window).scrollTop() == $(document).height() - $(window).height())
        {
            pauseScroll = true;
            loadNextBoothsPage(username, function() { pauseScroll = false; });
        }
    });
}

function loadNextBoothsPage(username, onAdditionalPagesAvailableCallback) {
    $('div#loadmoreajaxloader').show();
    $.ajax({
        url: baseUrl + "/_mobile/v2/userfeed.php",
        data: {
            boothername: username,
            pagenum: page + 1
        },
        type: "POST",
        dataType: "json",
        success: function(json)
        {
            if (json.success) {
                page++;
                $('div#loadmoreajaxloader').hide();
                renderBoothsFromData(json);
                onAdditionalPagesAvailableCallback.call();
            } else if (json.error) {
                $('div#loadmoreajaxloader').html('<center>'+json.error+'</center>');
                onAdditionalPagesAvailableCallback.call();
            } else {
                $('div#loadmoreajaxloader').html('<center>No more posts to show.</center>');
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            onAdditionalPagesAvailableCallback.call();
        }});
}


