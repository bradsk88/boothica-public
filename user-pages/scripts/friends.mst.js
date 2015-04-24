function loadFriendsList(username) {
    $.post("{{baseUrl}}/_mobile/v2/getfriendsof", {
        username: username
    }, function(data) {
        if ("undefined" === typeof(data.success)) {
            showError(data.error);
        }
        renderFromData(data);
    }, "json").fail(function() {
        showError("API Failure");
    });
}

var renderFromData = function(data) {
    $.get("{{baseUrl}}/user-pages/templates/userFriendCell.mst", function(template) {
        $.each(data.success.boothers, function(idx, obj) {
            var html = Mustache.render(template, {
                baseUrl: "{{baseUrl}}",
                bootherImageUrl: obj.bootherImageUrl,
                bootherDisplayname: obj.bootherDisplayName,
                bootherName: obj.bootherName

            });
            $("#friends_list").append(html);
        });
    });
};

function showError(error) {
    alert(error);
}
