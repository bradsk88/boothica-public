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
        .fail(function() {

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

};
