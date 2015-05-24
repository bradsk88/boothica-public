numPerPage = 10;

$(document).ready(function() {

    loadActivity();

});

var loadActivity = function() {

    $("activity_loader").show();

    loadNotificationsThenActivity();
    $.post("{{baseUrl}}/_mobile/v2/getactivity.php", {
            numperpage: numPerPage
    },
    function(data) {
        $("activity_loader").hide();
        if (data == null || "undefined" === typeof(data.success)) {
            $("#activity_feed").append("Error contacting server");
            return;
        }

        if (data.success) {

            renderActivityFeed(data);
            $("#body_load_more_button")[0].onclick = null;
            $("#body_load_more_button").click(function() { loadNextActivityPage(function(){}) });
            enableInfiniteScroll();
            return;
        }

        $("#activity_feed").append("failure: " + data.error);

    }, "json")
    .fail(function(_, s) {
        $("#activity_feed").append($("<img/>", {
            src: "{{baseUrl}}/media/failwhale.png"
        }));
        var button = $("<button/>", {
            type: "submit",
            html: "Contact us"
        });
        var formControls = $("<div/>", {
            class: "phoneAnalogButton floating",
            html: button
        });
            $("#activity_feed").append($("<form/>", {
                action: "{{baseUrl}}/info/contact",
                html: formControls
            }));
    });

};

var loadNotificationsThenActivity = function() {
    $.post("{{baseUrl}}/_mobile/v2/getnotifications.php", {
        },
        function(data) {
            $("activity_loader").hide();
            if (data == null || "undefined" === typeof(data.success)) {
                $("#activity_feed").append("Error contacting server");
                return;
            }

            if (data.success) {
                $.each(data.success.data, function(_, obj) {
                    $("#activity_feed").append($("<a/>", {
                        html: obj.text,
                        class: "friendRequestActivity",
                        href: obj.url
                    }));
                });
                return;
            }

            $("#activity_feed").append("failure: " + data.error);

        }, "json")
        .fail(function(_, s) {
            doLoadActivity();
        });
};

var doLoadActivity = function() {
    $.post("{{baseUrl}}/_mobile/v2/getactivity.php", {
            numperpage: numPerPage
    },
    function(data) {
        $("activity_loader").hide();
        if (data == null || "undefined" === typeof(data.success)) {
            $("#activity_feed").append("Error contacting server");
            return;
        }

        if (data.success) {
            renderActivityFeed(data);
            return;
        }

        $("#activity_feed").append("failure: " + data.error);

    }, "json")
    .fail(function(_, s) {
        $("#activity_feed").append($("<img/>", {
            src: "{{baseUrl}}/media/failwhale.png"
        }));
        var button = $("<button/>", {
            type: "submit",
            html: "Contact us"
        });
        var formControls = $("<div/>", {
            class: "phoneAnalogButton floating",
            html: button
        });
        $("#activity_feed").append($("<form/>", {
            action: "{{baseUrl}}/info/contact",
            html: formControls
        }));
    });
};

var renderActivityFeed = function(data) {

    items = data.success.data;

    if (data.success.data.length < numPerPage) {
        $("#body_load_more_button").hide();
    }

    if (items.length == 0) {
        $.get('{{baseUrl}}/activity/templates/noActivity.mst', function (template) {
            var html = Mustache.render(template, {});
            $("#activity_feed").append(html);
        });
        return;
    }

    $.get('{{baseUrl}}/comment/templates/textWithBooth.mst', function (textTemplate) {
        $.get('{{baseUrl}}/comment/templates/photoWithBooth.mst', function (photoTemplate) {
            renderActivityFeedUsingTemplates(items, textTemplate, photoTemplate);
        });
    });
};

var renderActivityFeedUsingTemplates = function(items, textTemplate, photoTemplate) {
    $.each(items, function(idx, obj) {
        var data = {
            username: obj.commenterName,
            bootherName: obj.bootherName,
            bootherDisplayName: obj.bootherDisplayName,
            boothNum: obj.boothNum,
            commenterImageUrl: obj.commenterImage,
            commenterDisplayName: obj.commenterDisplayName,
            boothImageUrl: obj.bootherImage,
            text: obj.commentText,
            boothShortDescription: obj.bootherName == obj.currentUserName ? "your booth" : obj.bootherDisplayName + "'s booth"
        };
        var template = textTemplate;
        if (obj.hasMedia) {
            $.extend(data, {
               photo: obj.commentMediaImage
            });
            template = photoTemplate
        }
        var html = Mustache.render(template, data);
        $("#activity_feed").append(html);
    });
};

var page = 1; //

function enableInfiniteScroll() {

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
            loadNextActivityPage(function() { pauseScroll = false; });
        }
    });
}

function loadNextActivityPage(onAdditionalPagesAvailableCallback) {
    $('div#loadmoreajaxloader').show();
    $.ajax({
        url: "{{baseUrl}}/_mobile/v2/getactivity.php",
        data: {
            numperpage: numPerPage,
            pagenum: page + 1
        },
        type: "POST",
        dataType: "json",
        success: function(json)
        {
            if (json.success) {
                page++;
                $('div#loadmoreajaxloader').hide();
                renderActivityFeed(json);
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

