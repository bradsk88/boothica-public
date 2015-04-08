<?php
/**
 * Created by PhpStorm.
 * User: Brad
 * Date: 3/16/15
 * Time: 9:00 PM
 */
require_once "{$_SERVER['DOCUMENT_ROOT']}/framing/PageFrame.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";
require_common("utils");
require_once "{$_SERVER['DOCUMENT_ROOT']}/booth/utils.php";

error_reporting(E_ALL);
if (!isset($_SESSION)) session_start();
main();

function main() {

    $root = base();

    $username = $_REQUEST['username'];
    $boothnum = $_REQUEST['boothnum'];

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

    $allowedToInteractWithBooth = isAllowedToInteractWithBooth($_SESSION['username'], $boothnum);

    $htmlBuilder = new h2o("{$_SERVER['DOCUMENT_ROOT']}/booth/templates/oneBoothFrame.mst");
    $html = $htmlBuilder->render(array(
        "baseUrl" => $root,
        "allowed" => $allowedToInteractWithBooth
    ));

    if (isLoggedIn() && $allowedToInteractWithBooth) {
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
    $page->css($root."/css/oneBooth-page.css");
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