<?PHP
header('Content-Type: application/javascript');

$prependage = '';
error_reporting(0);
if (strpos(__FILE__, '_dev')) {
    $prependage = '/_dev';
    error_reporting(E_ALL);
}

require_once("{$_SERVER['DOCUMENT_ROOT']}" . $prependage . "/common/boiler.php");

session_start();
main();

function main()
{

    $base = base();

    echo <<<EOT

    var baseUrl = "$base";

    function loadOneBooth(boothnum) {
        $.post(baseUrl + "/_mobile/v2/getbooth.php", {
            boothnum: boothnum
        }, function (data) {
            renderOneBoothFromData(data);
        }, "json")
        .fail(function (jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        })
    }

    var renderOneBoothFromData = function(data) {
        $.get(baseUrl + '/framing/templates/oneBooth.mst', function(template) {
            var html = "";
            if (typeof(data.success) === "undefined") {
                html = "error: " + data.error;
                return;
            }
            html += Mustache.render(template, {
                boothImageUrl:data.success.absoluteImageUrl,
                blurb: data.success.blurb,
                boothNumber: data.success.boothnum
            });
            $("#user_booth_body").html(html);
            loadOneBoothComments(data.success.boothnum)
        });
    };

    var loadOneBoothComments = function(boothnum) {
        var spinner = $("<img/>", {
            src: baseUrl + "/media/ajax-loader.gif",
            style: "margin: 0 auto;"
        });
        var loader = $("<div/>", {
            id: "loadmoreajaxloader",
            html: spinner
        });
        $("#user_booth_body").append(loader);

        $.post(baseUrl + "/_mobile/v2/getcomments.php", {
            boothnum: boothnum
        }, function (data) {
            renderOneBoothCommentsFromData(data);
            $(spinner).hide();
        }, "json")
        .fail(function (jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        })

    };

    var renderOneBoothCommentsFromData = function(data) {
        $.get(baseUrl + '/framing/templates/textCommentNoContext.mst', function(template) {
            var html = "";
            if (typeof(data.success) === "undefined") {
                html = "error: " + data.error;
                return;
            }
            $.each(data.success, function(idx, obj) {
                html += Mustache.render(template, {
                    text: obj.commenttext,
                    username: obj.commentername,
                    displayName: obj.commenterdisplayname,
                    imageUrl: obj.absoluteIconImageUrl,
                    baseUrl: baseUrl
                });
            });
            $("#user_booth_body").append(html);
        });
    };

EOT;
}
