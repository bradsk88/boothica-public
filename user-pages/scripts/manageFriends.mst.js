function loadIncomingFriendRequests(username) {

    $("#inbound_requests").html(makeSpinner());
    $.post("{{baseUrl}}/_mobile/v2/getincomingfriendrequests", {
    },
    function(data) {
        renderFriendRequests(data, "#inbound_requests", "{{baseUrl}}/user-pages/templates/friendRequest.mst", false);
    }, "json")
    .fail(function() {
        $(div).append($("<div/>", {
            class: "phoneAnalogText error",
            html: "Unexpected Error"
        }));
    });
}


function loadOutboundFriendRequests(username) {
    $("#outbound_requests").html(makeSpinner());
    $.post("{{baseUrl}}/_mobile/v2/getoutboundfriendrequests", {
        },
        function(data) {
            renderFriendRequests(data, "#outbound_requests", "{{baseUrl}}/user-pages/templates/outboundRequest.mst", true);
        }, "json")
        .fail(function() {
            $(div).append($("<div/>", {
                class: "phoneAnalogText error",
                html: "Unexpected Error"
            }));
        });
}


function loadIgnoredFriendRequests(username) {
    $("#ignored_requests").html(makeSpinner());
    $.post("{{baseUrl}}/_mobile/v2/getignoredfriendrequests", {
    },
    function(data) {
        renderFriendRequests(data, "#ignored_requests", "{{baseUrl}}/user-pages/templates/ignoredRequest.mst", true);
    }, "json")
    .fail(function() {
        $(div).append($("<div/>", {
            class: "phoneAnalogText error",
            html: "Unexpected Error"
        }));
    });
}

var renderFriendRequests = function(jsonData, div, templatePath, roundBottom) {
    if ("undefined" === typeof(jsonData.success)) {
        $.each($(div).find("#ajaxspinner"), function(_, obj) { obj.remove() });
        $(div).append($("<div/>", {
            class: "phoneAnalogText error",
            html: jsonData.error ? jsonData.error : "Unexpected error"
        }));
        return;
    }
    $.each($(div).find("#ajaxspinner"), function(_, obj) { obj.remove() });

    if (jsonData.success.requests.length == 0) {
        $(div).append($("<div/>", {
            class: "phoneAnalogText bottom",
            html: "None"
        }));
    }

    $.get(templatePath, function(template) {
        $.each(jsonData.success.requests, function(idx, request) {
            var html = Mustache.render(template, {
                requesterDisplayName: request.displayName,
                requesterName: request.username,
                requesterImage: request.userImageAbsoluteUrl,
                username: jsonData.apiUsername,
                baseUrl: "{{baseUrl}}",
                bottom: idx == jsonData.success.requests.length -1
            });
            $(div).append(html);
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
