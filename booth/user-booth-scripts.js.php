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
        $.post(baseUrl + "/_mobile/v2/userfeed.php", {
            boothername: username,
            pagenum: 1,
            numperpage: 9
        }, function (data) {
            renderBoothsFromData(data);
        }, "json")
        .fail(function (jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        })
    }

    var renderBoothsFromData = function(data) {
        $.get(baseUrl + '/framing/templates/centerBooth.mst', function(template) {
            var html = "";
            if (typeof(data.success) === "undefined") {
                html = "error: " + data.error;
                return;
            }
            $.each(data.success.booths, function (idx, obj) {
                html += Mustache.render(template, {
                    thumbnail: obj.absoluteImageUrlThumbnail,
                    blurb: decodeURI(obj.blurb),
                    boothUrl: baseUrl + "/users/" + obj.boothername + "/" + obj.boothnum,
                    boothNumber: obj.boothnum
                });
                loadCommentCounts(obj.boothnum);
            });
            $("#user_booths_feed").append(html);
        });
    };

    var loadCommentCounts = function(boothnum) {
        $.post(baseUrl + "/_mobile/v2/getcommentcount.php", {
            boothnum: boothnum
        }, function (data) {
            if (data.success) {
                var text = data.success.count + " comments...";
                if (data.success.count == 1) {
                    text = data.success.count + " comment...";
                }
                $("#centerBoothOpenButtonText"+boothnum).text(text);
            }
        }, "json")
        .fail(function (jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        })
    };

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
                        $('div#loadmoreajaxloader').hide();
                        renderBoothsFromData(json);
                        pauseScroll = false;
                    } else if (json.error) {
                        $('div#loadmoreajaxloader').html('<center>'+json.error+'</center>');
                        pauseScroll = false;
                    } else {
                        $('div#loadmoreajaxloader').html('<center>No more posts to show.</center>');
                        pauseScroll = true;
                    }
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
