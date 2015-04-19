$(function() {
    $("#requires_js_warning").remove();
});

$(window).bind("load", function() {

});

function loadRandomBooths() {
    var html = '<div id = "random_booths_feed"></div>' +
        '<div id="loadmoreajaxloader" style="display:none;">' +
        '<center><img src="{{baseUrl}}/media/ajax-loader.gif" /></center>' +
        '</div>';

    $.post("{{baseUrl}}/_mobile/v2/randompublicbooths.php", {
        pagenum: 1,
        numperpage: 4
    }, function (data) {
        renderSideBoothsFromDataAsync(data, "#firstSideBarContents");
    }, "json");

    return html;
}

function loadNewFriendsBooths() {
    var html = '<div id = "friend_booths_feed"></div>' +
        '<div id="loadmoreajaxloader" style="display:none;">' +
        '<center><img src="{{baseUrl}}/media/ajax-loader.gif" /></center>' +
        '</div>';

    $.post("{{baseUrl}}/_mobile/v2/friendfeed.php", {
        pagenum: 1,
        numperpage: 4
    }, function (data) {
        renderSideBoothsFromDataAsync(data, "#firstSideBarContents");
    }, "json");

    return html;
}

function loadPublicBooths() {
    var html = '<div id = "public_booths_feed"></div>' +
        '<div id="loadmoreajaxloader" style="display:none;">' +
        '<center><img src="{{baseUrl}}/media/ajax-loader.gif" /></center>' +
        '</div>';

    $.post("{{baseUrl}}/_mobile/v2/publicfeed.php", {
        pagenum: 1,
        numperpage: 4,
        includeFriends: false
    }, function (data) {
        renderSideBoothsFromDataAsync(data, "#lastSideBarContents");
    }, "json");

    return html;
}

var renderSideBoothsFromDataAsync = function(data, div) {
    $.get('{{baseUrl}}/framing/templates/sideBooth.mst', function(template) {
        var html = "";
        if (typeof(data.success) === "undefined") {
            html = "error: " + data.error;
            return;
        }
        $.each(data.success.booths, function (idx, obj) {
            html += Mustache.render(template, {
                displayname: obj.bootherdisplayname,
                bootherBoothsUrl: "{{baseUrl}}/users/" + obj.boothername + "/booths",
                boothUrl: "{{baseUrl}}/users/" + obj.boothername + "/" + obj.boothnum,
                thumbnail: obj.absoluteImageUrlThumbnail,
                blurb: obj.blurb,
                commentsCount: 10
            });
        });
        $(div).append(html);
    });
};
