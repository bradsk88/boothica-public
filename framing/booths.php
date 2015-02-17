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
    $page = new PageFrame();
    $page->body("<div id = \"user_booths_feed\"></div>");
    $page->firstSideBar(makeRandomBoothsSideBar(), "Random Booths", false);
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