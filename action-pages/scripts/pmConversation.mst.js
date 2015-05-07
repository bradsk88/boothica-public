function loadPMs(otherUserName) {

    $.post("{{baseUrl}}/_mobile/v2/getprivatemessages", {
        otherUsername: otherUserName,
        markread: true
    }, function(data) {
        renderPrivateMessageConversationFromData(data);
    }, "json").
    fail(function() {
        showError("API Fail");
    });

}

var renderPrivateMessageConversationFromData = function(data) {
    if ("undefined" === typeof(data.success)) {
        showError("undefined" === typeof(data.error) ? "Unexpected error" : data.error);
        return;
    }
    if (data.success.messages.length == 0) {
        showError("No messages");
        return;
    }
    $.get("{{baseUrl}}/action-pages/templates/pmConversationCell.mst", function(template) {
        $.each(data.success.messages, function (idx, obj) {
            var html = Mustache.render(template, {
                userDisplayName: obj.otherUserDisplayName,
                text: obj.text,
                mine: data.apiUsername == obj.otherUsername
            });
            $("#pm_convo_list").append(html);
        });
    });
};

var showError = function(error) {
    $("#pm_convo_list").html($("<div/>", {
        html: error
    }))
};
