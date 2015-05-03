function loadPMs(otherUserName) {

    $.post("{{baseUrl}}/_mobile/v2/getprivatemessages", {
        otherUsername: otherUserName
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
    $.each(data.success.messages, function(idx, obj) {
        html = Mustache.render("{{baseUrl}}/action-pages/templates/pmConversationCell.mst", {
            userDisplayName: obj.userDisplayName,
            text: obj.text
        })
    });
};

var showError = function(error) {
    $("#pm_convo_list").html($("<div/>", {
        html: error
    }))
};
