function loadBootherConsole(username) {

    $.post("{{baseUrl}}/_mobile/vBeta/getuser.php", {
        boothername: username
    }, function (data) {
        if (data.success) {
            $.get('{{baseUrl}}/user-pages/templates/bootherConsole.mst', function(template) {
                html = Mustache.render(template, {
                    baseUrl: "{{baseUrl}}",
                    username: data.apiUsername,
                    bootherName: username,
                    bootherDisplayPhoto: data.success.displayPhotoAbsoluteUrl,
                    bootherDisplayName: data.success.displayName,
                    bootherPluralName: data.success.pluralDisplayName
                });
                $("#inner_body").prepend(html);
            });
        } else if (data.error) {
            $("#inner_body").prepend("Error: " + data.error);
        }
    }, "json");

}

function loadOwnerConsole(username) {

    $.post("{{baseUrl}}/_mobile/vBeta/getuser.php", {
        boothername: username
    }, function (data) {
        if (data.success) {
            $.get('{{baseUrl}}/user-pages/templates/ownerConsole.mst', function(template) {
                html = Mustache.render(template, {
                    baseUrl: "{{baseUrl}}",
                    username: data.apiUsername,
                    bootherName: username,
                    bootherDisplayPhoto: data.success.displayPhotoAbsoluteUrl,
                    bootherDisplayName: data.success.displayName,
                    bootherPluralName: data.success.pluralDisplayName
                });
                $("#inner_body").prepend(html);
            });
        } else if (data.error) {
            $("#inner_body").prepend("Error: " + data.error);
        }
    }, "json");

}
