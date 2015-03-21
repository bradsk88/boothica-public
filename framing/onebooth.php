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
if (!isset($_SESSION)) session_start();
main();

function main() {

    $root = base();

    $username = 'tester2';
    $boothnum = 18304;

    //TODO: check if username and booth number are sympatico.  If not, redirect.
    //TODO: Add photo comment display
    //TODO: Add photo comment input
    //TODO: Add booth liking
    //TODO: Add link to user's profile or booths
    //TODO: Add follow button
    //TODO: Add like comments
    //TODO: Add delete comments (for boother/mods)
    //TODO: Add edit blurb (for boother)
    //TODO: Add ability to see who has liked

    //TODO: h2o this \/
    $html = <<<EOT
    <div class = "section_toggler" id = "user_booth_body_toggler">
        $username - Booth #$boothnum
    </div>
    <div class = "userBoothBody" id = "user_booth_body"></div>
    <div class = "userBoothButtonsRegion">
        <div class = "userBoothButton" id = "like_booth_button" onclick = "likeBooth($boothnum, '$username')">
            <div class = "boothLikesCounterRegion">
                <div id = "booth_likes_counter">0</div>
            </div>
            Like
        </div>
    </div>
    <div class = "userBoothComments" id = "user_booth_comments"></div>
    <div id="loadmoreajaxloader" style="display:none;">
        <center><img src="$root/media/ajax-loader.gif" /></center>
    </div>
EOT;

    if (isLoggedIn()) {
        $commentInputH2O = new h2o("{$_SERVER['DOCUMENT_ROOT']}/framing/templates/textCommentInput.mst");
        $html .= $commentInputH2O->render(array(
            "baseUrl" => $root,
            "boothername" => $username,
            "boothnum" => $boothnum
        ));
    }

    $page = new PageFrame();
    $page->body($html);
    $page->script($root."/booth/onebooth-scripts.js");
    $pagescripts = new h2o("{$_SERVER['DOCUMENT_ROOT']}/user-pages/onebooth-page-script.mst");
    $page->rawScript($pagescripts->render(array(
        "username" => $username,
        "boothnum" => $boothnum,
        "loggedIn" => isset($_SESSION['username'])
    )));
    $page->css($root."/css/posts.css");
    $page->css($root."/css/onebooth-page.css");
    $page->css($root."/css/textcomment-nocontext.css");
    $page->css($root."/css/textcomment-input.css");
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