$(document).ready(function() {
    $.post("{{baseUrl}}/_mobile/v2/getprivatemessagessummary", {
    }, function(data) {
        renderPrivateMessageFaces(data);
    }, "json").fail(function() {
        showError("API Error");
    });
});

var renderPrivateMessageFaces = function(data) {
    if ("undefined" === typeof(data.success)) {
        showError("undefined" === typeof(data.error) ? "Unexpected Error" : data.error);
        return;
    }
    $.get("{{baseUrl}}/user-pages/templates/privateMessage.mst", function(template) {
        $.each(data.success.users, function(idx, obj) {
            var html = Mustache.render(template, {
                userImageUrl: obj.absoluteIconImageUrl,
                isNew: obj.hasnew,
                username: obj.username,
                baseUrl: "{{baseUrl}}"
            });
            $("#private_messages").append(html);
        });
    });
};

var showError = function(message) {
    $.get("{{baseUrl}}/user-pages/templates/pmError.mst", function(template) {
        var html = Mustache.render(template, {
            message: message,
            baseUrl: "{{baseUrl}}"
        });
        $("#private_messages").html(html);
    });
};
