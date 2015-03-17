<?php
/**
 * Created by PhpStorm.
 * User: Brad
 * Date: 3/16/15
 * Time: 9:00 PM
 */
require_once "{$_SERVER['DOCUMENT_ROOT']}/framing/PageFrame.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";

error_reporting(E_ALL);
if (!isset($_SESSION['username'])) session_start();
main();

function main() {

    $root = base();

    $username = 'bradsk88';
    $boothnum = 1;

    //TODO: check if username and booth number are sympatico.  If not, redirect.
    //TODO: Add photo comment display
    //TODO: Add text comment input
    //TODO: Add photo comment input
    //TODO: Add booth liking
    //TODO: Add link to user's profile or booths
    //TODO: Add follow button
    //TODO: Add like comments
    //TODO: Add delete comments (for boother/mods)
    //TODO: Add edit blurb (for boother)

    $html = <<<EOT
    <div class = "section_toggler" id = "user_booth_body_toggler">
        $username - Booth #$boothnum
    </div>
    <div class = "user_booth_body" id = "user_booth_body"></div>
    <div id="loadmoreajaxloader" style="display:none;">
        <center><img src="$root/media/ajax-loader.gif" /></center>
    </div>
EOT;

    $page = new PageFrame();
    $page->body($html);
    $page->script($root."/booth/onebooth-scripts.js");
    $pagescripts = new h2o("../user-pages/onebooth-page-script.mst");
    $page->rawScript($pagescripts->render(array(
        "username" => $username,
        "boothnum" => $boothnum,
        "loggedIn" => isset($_SESSION['username'])
    )));
    $page->css($root."/css/posts.css");
    $page->css($root."/css/onebooth-page.css");
    $page->css($root."/css/textcomment-nocontext.css");
    initializeSideBars($page);
    $page->echoHtml();

}

// TODO: This is duplicated in most pages.  Maybe move it to PageFrame
function initializeSideBars($page) {

    if (isset($_SESSION['username'])) {
        $page->firstSideBar("New Friend Booths", false);
    } else {
        $page->firstSideBar("Random Booths", false);
    }
    $page->lastSideBar("New Public Booths");
}