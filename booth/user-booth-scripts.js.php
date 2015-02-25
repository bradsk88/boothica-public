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

    //declare event to run when div is visible
    function loadUserBooths(username){
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

        if (jsonData.success == "undefined") {
            return "error" + jsonData.error;
        }
        return makeHTMLFromSuccess(jsonData.success)
    }

    function makeHTMLFromSuccess(success) {
        html = "";
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

    function enableInfiniteScroll(username) {
        var page = 2;
        var pauseScroll = false;
        $(window).scroll(function()
        {
            if (pauseScroll) {
                return;
            }
            if($(window).scrollTop() == $(document).height() - $(window).height())
            {
                pauseScroll = true;
                $('div#loadmoreajaxloader').show();
                $.ajax({
                url: baseUrl + "/_mobile/v2/userfeed.php",
                data: {
                    boothername: username,
                    pagenum: page
                },
                type: "POST",
                dataType: "json",
                success: function(json)
                {
                    if (json.success) {
                        page++;
                        $("#user_booths_feed").append(makeHTMLFromSuccess(json.success));
                        $('div#loadmoreajaxloader').hide();
                    } else if (json.error) {
                        $('div#loadmoreajaxloader').html('<center>'+json.error+'</center>');
                    } else {
                        $('div#loadmoreajaxloader').html('<center>No more posts to show.</center>');
                    }
                    pauseScroll = false;
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert(textStatus);
                    pauseScroll = false;
                }});
            }
        });
    }



EOT;
}
