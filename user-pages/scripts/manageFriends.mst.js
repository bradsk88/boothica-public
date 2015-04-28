function loadIncomingFriendRequests(username) {

    $("#inbound_requests").html(makeSpinner());
    $.post("{{baseUrl}}/_mobile/v2/getincomingfriendrequests", {
        },
        function(data) {
            renderIncomingFriendRequests(data);
        }, "json")
        .fail(function() {

        });
}

var renderIncomingFriendRequests = function(jsonData) {

    if ("undefined" === typeof(jsonData.success)) {
        $.each($("#inbound_requests").find("#ajaxspinner"), function(_, obj) { obj.remove() });
        $("#inbound_requests").append("<div>Error loading</div>");
        return;
    }
    $.each($("#inbound_requests").find("#ajaxspinner"), function(_, obj) { obj.remove() });
    $.get("{{baseUrl}}/user-pages/templates/friendRequest.mst", function(template) {
        $.each(jsonData.success.requests, function(_, request) {
            var html = Mustache.render(template, {
                requesterDisplayName: request.displayName,
                requesterName: request.username,
                requesterImage: request.userImageAbsoluteUrl,
                username: jsonData.apiUsername,
                baseUrl: "{{baseUrl}}"
            });
            $(html).addClass("incoming");
            $("#inbound_requests").append(html);
        });
    });
};

var makeSpinner = function() {
    var spinner = $("<img/>", {
        src: "{{baseUrl}}/media/ajax-loader.gif",
        style: "margin: 0 auto;"
    });
    return $("<div/>", {
        id: "ajaxspinner",
        html: spinner
    });
};
