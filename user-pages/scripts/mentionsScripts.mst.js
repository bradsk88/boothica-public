numPerPage = 10;

$(document).ready(function() {
    $.post("{{baseUrl}}/_mobile/v2/getmentions", {
        numperpage: numPerPage
    }, function(data) {
        renderMentions(data);
        $("#body_load_more_button")[0].onclick = null;
        $("#body_load_more_button").click(function() { loadNextMentionsPage(function(){}) });
        enableInfiniteScroll()
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
    if (data.success.mentions.length < numPerPage) {
        $("#body_load_more_button").hide();
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
            loadNextMentionsPage(function() { pauseScroll = false; });
        }
    });
}

function loadNextMentionsPage(onAdditionalPagesAvailableCallback) {
    $('div#loadmoreajaxloader').show();
    $.ajax({
        url: "{{baseUrl}}/_mobile/v2/getmentions.php",
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
                renderMentions(json);
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

