<?php
/**
 * Created by PhpStorm.
 * User: Brad
 * Date: 2/14/15
 * Time: 7:23 PM
 */

require_once "{$_SERVER['DOCUMENT_ROOT']}/framing/PageFrame.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";

error_reporting(0);
session_start();
main();

function main() {

    $root = base();

    $html = <<<EOT
    <div class = "section_toggler" id = "user_booths_feed_toggler">
        Booths from this user
    </div>
    <div class = "primary_booths_feed" id = "user_booths_feed"></div>
    <div id="loadmoreajaxloader" style="display:none;">
        <center><img src="$root/media/ajax-loader.gif" /></center>
    </div>
EOT;

    $page = new PageFrame();
    $page->body($html);
    $page->useDefaultSideBars();
    $page->loadPublicSidebarsContent();
    $page->script($root."/public-feed/friend-feed-scripts.js"); //TODO: Relocate this
    $username = $_GET['username'];
    $page->title($username ." on ".basePretty());
    $page->rawScript("<script type = \"text/javascript\">
        $(document).ready(function() {
            loadFriendBoothsMain();
            $(\"#body_load_more_button\")[0].onclick = null;
            $(\"#body_load_more_button\").click(function() { loadNextBoothsPage(\"".$username."\", function(){}) });
        });
    </script>");
    $page->css($root."/css/posts.css");
    $page->css($root."/css/posts-page.css");
    $page->echoHtml();

}
