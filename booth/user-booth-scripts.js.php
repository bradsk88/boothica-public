<?PHP

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

$boothcellHTML = <<<EOT
<div class = "center_booth">
    <img src = "">
</div>
EOT;

function main() {

$base = base();
$user = "alyen";

echo <<<EOT

    var baseUrl = "$base";
    var username = "$user";

    //declare event to run when div is visible
    function loadUserBooths(){
        $.post("$base/_mobile/v2/userfeed.php", {
            boothername: username,
            pagenum: 1,
            numperpage: 9
        }, function (data) {
            $("#user_booths_feed").append(makeUserFeedGridCellsHTML(data, username));
        }, "json")
        .fail(function (jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        })
    }

    function makeUserFeedGridCellsHTML(jsonData, username) {
        html = "";
        if (jsonData.success == "undefined") {
            return "error" + jsonData.error;
        }
        var success = jsonData.success;
        var booths = success.booths;
        $.each(booths, function (idx, obj) {
            cellHTML =
            "<div class = 'centerBooth'>" +
                "<div class = 'centerBoothImageRegion'>" +
                "   <img class = 'centerBoothImage' src = '"+obj.absoluteImageUrlThumbnail+"' width='100%'>"+
                "</div>" +
                "<div class = 'centerBoothOpenButton'>" +
                "    10 Comments" +
                "</div>" +
                "<div class = 'centerBoothText'>" +
                    obj.blurb +
                "</div>" +
            "</div>";
            html += cellHTML;
        });
        return html;
    }

EOT;
}
