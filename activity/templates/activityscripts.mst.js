$(document).ready(function() {

    loadActivity();
    //TODO: Make this decision depending on login status;
    loadNewFriendsBooths();
    loadPublicBooths();

});

var loadActivity = function() {

    $("activity_loader").show();
    $.post("{{baseUrl}}/_mobile/v2/activity.php", {
    },
    function(data) {
        $("activity_loader").hide();
        if (data == null || "undefined" === typeof(data.success)) {
            $("#activity_feed").append("Error contacting server");
            return;
        }

        if (data.success) {
            renderActivityFeed(data.success);
            return;
        }

        $("#activity_feed").append("failure: " + data.error);

    }, "json")
    .fail(function(_, s) {
        $("#activity_feed").append($("<img/>", {
            src: "{{baseUrl}}/media/failwhale.png"
        }));
    });

};

var renderActivityFeed = function(items) {

    if (items.length == 0) {
        $.get('{{baseUrl}}/activity/templates/noActivity.mst', function (template) {
            var html = Mustache.render(template, {});
            $("#activity_feed").append(html);
        });
        return;
    }
    $.get('{{baseUrl}}/comment/templates/textWithBooth.mst', function (template) {
        $.each(items, function(idx, obj) {
            var html = Mustache.render(template, {
                username: obj.commenterName,
                bootherName: obj.bootherName,
                bootherDisplayName: obj.bootherDisplayName,
                boothNum: obj.boothNum,
                commenterImageUrl: obj.commenterImage,
                commenterDisplayName: obj.commenterDisplayName,
                boothImageUrl: obj.bootherImage,
                text: obj.commentText,
                boothShortDescription: obj.bootherName == obj.currentUserName ? "your booth" : obj.bootherDisplayName + "'s booth"
            });
            $("#activity_feed").append(html);
        });
    });
};
