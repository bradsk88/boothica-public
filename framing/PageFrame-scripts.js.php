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
            $("#random_booths_feed").append(makeSideBoothFeedGridCellsHTML(data));
        }, "json")
        .fail(function (jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        });

        return html;
    }

    function makeSideBoothFeedGridCellsHTML(jsonData) {

        if (typeof(jsonData.success) === "undefined") {
            return "error " + jsonData.error;
        }
        return makeSideBoothHTMLFromSuccess(jsonData.success);
    }

    function makeSideBoothHTMLFromSuccess(success) {
        html = "";
        var booths = success.booths;
        $.each(booths, function (idx, obj) {
            cellHTML =
            "<div class = 'sideBooth'>" +
                "<div class = 'sideBoothImageRegion'>" +
                "   <img class = 'sideBoothImage' src = '"+obj.absoluteImageUrlThumbnail+"' width='100%'>"+
                "</div>" +
                "<div class = 'sideBoothOpenButton'>" +
                "    10 Comments" +
                "</div>" +
                "<div class = 'sideBoothText'>" +
                    obj.blurb +
                "</div>" +
            "</div>";
            html += cellHTML;
        });
        return html;
    }

EOT;

}

