$(document).ready(function() {
    $.post("{{baseUrl}}/_mobile/v2/getmentions", {
    }, function(data) {
        renderMentions(data);
    }, "json").fail(function() {
        showError("API Error");
    });
});

var renderMentions = function(data) {
    if ("undefined" === typeof(data.success) || "undefined" === typeof(data.success.mentions)) {
        showError("undefined" === typeof(data.error) ? "Unexpected Error" : data.error);
        return;
    }
    if (data.success.contains_new) {
        var button = $("<button/>", {
            html: "Mark all as read"
        });
        var clearAll = $("<form/>", {
            action: "{{baseUrl}}/mentions/clearall",
            method: "POST",
            class: "phoneAnalogButton floating",
            html: button
        });
        $("#mentions").append(clearAll);
    }
    $.get("{{baseUrl}}/comment/templates/textWithBooth.mst", function(template) {
        $.each(data.success.mentions, function(idx, obj) {
            var html = Mustache.render(template, {
                baseUrl: "{{baseUrl}}"
                , boothNum: obj.boothNumber
                , bootherName: obj.bootherUsername
                , boothShortDescription: data.apiUsername == obj.bootherUsername ? "your booth" : obj.bootherPosessiveDisplayname + " booth"
                , commenterImageUrl: obj.mentionerIconAbsoluteImageUrl
                , commenterDisplayName: obj.mentionerDisplayname
                , boothImageUrl: obj.boothIconAbsoluteImageUrl
                , text: obj.text
                , actionDescription: "mentioned you"
            });
            $("#mentions").append(html);
        });
    });
};

var showError = function(message) {
    $.get("{{baseUrl}}/framing/templates/error.mst", function(template) {
        var html = Mustache.render(template, {
            longError: message,
            baseUrl: "{{baseUrl}}"
        });
        $("#mentions").html(html);
    });
};
