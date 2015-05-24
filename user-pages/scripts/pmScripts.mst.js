numPerPage = 6;

$(document).ready(function() {
    $.post("{{baseUrl}}/_mobile/v2/getprivatemessagessummary", {
        numperpage: numPerPage
    }, function(data) {
        renderPrivateMessageFaces(data);
        $("#body_load_more_button")[0].onclick = null;
        $("#body_load_more_button").click(function() { loadNextPMsPage(function(){}) });
        enableInfiniteScroll()
    }, "json").fail(function() {
        showError("API Error");
    });
});

var renderPrivateMessageFaces = function(data) {
    if ("undefined" === typeof(data.success)) {
        showError("undefined" === typeof(data.error) ? "Unexpected Error" : data.error);
        return;
    }
    if (data.success.users.length < numPerPage) {
        $("#body_load_more_button").hide();
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
            loadNextPMsPage(function() { pauseScroll = false; });
        }
    });
}

function loadNextPMsPage(onAdditionalPagesAvailableCallback) {
    $('div#loadmoreajaxloader').show();
    $.ajax({
        url: "{{baseUrl}}/_mobile/v2/getprivatemessagessummary.php",
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
                renderPrivateMessageFaces(json);
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
