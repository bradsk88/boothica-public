<?PHP

# This is the most recent version of the "user booths" scriptset as of Feb 25, 2015.

header('Content-Type: application/javascript');

$prependage = '';
error_reporting(0);
if (strpos(__FILE__, '_dev')) {
    $prependage = '/_dev';
    error_reporting(E_ALL);
}

require_once("{$_SERVER['DOCUMENT_ROOT']}".$prependage."/common/boiler.php");

session_start();
main();

function main() {

    $base = base();

    echo <<<EOT

    var baseUrl = "$base";

    $(window).bind("load", function() {

    });

    function makeRandomBoothsSideSection() {

    }

    function loadRandomBooths() {
        var html = '<div id = "random_booths_feed"></div>' +
        '<div id="loadmoreajaxloader" style="display:none;">' +
            '<center><img src="'+baseUrl+'/media/ajax-loader.gif" /></center>' +
        '</div>';

        $.post(baseUrl + "/_mobile/v2/randompublicbooths.php", {
            pagenum: 1,
            numperpage: 9
        }, function (data) {
            renderSideBoothsFromDataAsync(data, "#random_booths_feed");
        }, "json")
        .fail(function (jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        });

        return html;
    }

    var renderSideBoothsFromDataAsync = function(data, div) {
        $.get(baseUrl + '/framing/templates/sideBooth.mst', function(template) {
            var html = "";
            if (typeof(data.success) === "undefined") {
                html = "error: " + data.error;
                return;
            }
            $.each(data.success.booths, function (idx, obj) {
                html += Mustache.render(template, {
                    thumbnail: obj.absoluteImageUrlThumbnail,
                    blurb: obj.blurb,
                    commentsCount: 10
                });
            });
            $(div).append(html);
        });
    };

EOT;

}

