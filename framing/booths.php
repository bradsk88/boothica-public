<?php
/**
 * Created by PhpStorm.
 * User: Brad
 * Date: 2/14/15
 * Time: 7:23 PM
 */

require_once "{$_SERVER['DOCUMENT_ROOT']}/framing/PageFrame.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";

main();

function main() {

    $root = base();

    $html = <<<EOT
    <div class = "primary_booths_feed" id = "user_booths_feed"></div>
    <div id="loadmoreajaxloader" style="display:none;">
        <center><img src="$root/media/ajax-loader.gif" /></center>
    </div>
EOT;

    $page = new PageFrame();
    $page->body($html);
    $page->firstSideBar("<div id = 'random_booths_feed'></div>", "Random Booths", false);
    $page->lastSideBar(makePublicFeedSideBar(), "New Public Booths");
    $page->script($root."/booth/user-booth-scripts.js");
    $page->script($root."/booth/user-booth-page-scripts.js");
    $page->css("posts.css");
    $page->echoHtml();

}


function makeRandomBoothsSideBar() {
    //TODO: This
    return "<div style = \"background-color: #F00; width: 100%; height: 30em;\">
    Random Booths
    </div>";
}

function makePublicFeedSideBar() {
    //TODO: This
    return "<div style = \"background-color: #0F0; width: 100%; height: 30em;\">
    PublicFeed
    </div>";
}