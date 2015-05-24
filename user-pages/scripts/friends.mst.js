numPerPage = 1;

function loadFriendsList(username) {
    $.post("{{baseUrl}}/_mobile/v2/getfriendsof", {
        boothername: username,
        numperpage: numPerPage
    }, function(data) {
        if ("undefined" === typeof(data.success)) {
            showError(data.error);
            return;
        }
        renderFromData(data);
        $("#body_load_more_button")[0].onclick = null;
        $("#body_load_more_button").click(function() { loadNextFriendsPage(username, function(){}) });
        enableInfiniteScroll(username);
    }, "json").fail(function(a, b) {
        showError("API Failure");
    });
}

var renderFromData = function(data) {
    if (data.success.boothers.length < numPerPage) {
        $("#body_load_more_button").hide();
    }
    $.get("{{baseUrl}}/user-pages/templates/userFriendCell.mst", function(template) {
        $.each(data.success.boothers, function(idx, obj) {
            var html = Mustache.render(template, {
                baseUrl: "{{baseUrl}}",
                bootherImageUrl: obj.bootherImageUrl,
                bootherDisplayname: obj.bootherDisplayName,
                bootherName: obj.bootherName,
                username: data.apiUsername
            });
            $("#friends_list").append(html);
        });
    });
};

function showError(error) {
    //alert(error);
}

var page = 1; //

function enableInfiniteScroll(username) {

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
            loadNextFriendsPage(username, function() { pauseScroll = false; });
        }
    });
}

function loadNextFriendsPage(username, onAdditionalPagesAvailableCallback) {
    $('div#loadmoreajaxloader').show();
    $.ajax({
        url: "{{baseUrl}}/_mobile/v2/getfriendsof.php",
        data: {
            boothername: username,
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
                renderFromData(json);
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
