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

    $boothowner = getBoothOwner($boothnum);
    if ($username != $boothowner) {
        $page = new PageFrame();
        $page->setBodyTemplateAndValues("{$_SERVER['DOCUMENT_ROOT']}/user-pages/templates/boothNumberMismatch.mst",
                                        array(
            "boothNumber" => $boothnum,
            "realOwner" => $boothowner,
            "realOwnerDisplayname" => getDisplayName($boothowner),
            "givenUsername" => $username,
            "givenUserDisplayname" => getDisplayName($username)
        ));
        $page->echoHtml();
        return;
    }

    //TODO: Check if booth is deleted
    //TODO: Add photo comment display
    //TODO: Add photo comment input
    //TODO START TONIGHT: Add booth liking
    //TODO START TONIGHT: Add link to user's profile or booths
    //TODO: Add follow button
    //TODO: Add like comments
    //TODO: Add delete comments (for boother/mods)
    //TODO START TONIGHT: Add edit blurb (for boother)
    //TODO START TONIGHT: Add ability to see who has liked

    $allowedToInteractWithBooth = isAllowedToInteractWithBooth($_SESSION['username'], $boothnum);


    if (isLoggedIn() && $allowedToInteractWithBooth) {
        $commentInputH2O = new h2o("{$_SERVER['DOCUMENT_ROOT']}/framing/templates/textCommentInput.mst");
        $commentInputHTML = $commentInputH2O->render(array(
            "baseUrl" => $root,
            "boothername" => $username,
            "boothnum" => $boothnum
        ));
    }

    $htmlBuilder = new h2o("{$_SERVER['DOCUMENT_ROOT']}/user-pages/templates/oneBoothFrame.mst");
    $html = $htmlBuilder->render(array(
        "baseUrl" => $root,
        "allowed" => $allowedToInteractWithBooth,
        "isOwner" => $_SESSION['username'] == $username,
        "commentInput" => $commentInputHTML,
        "username" => $username,
        "boothNumber" => $boothnum
    ));

    $page = new PageFrame();
    $page->body($html);
    $page->script($root."/booth/onebooth-scripts.js");
    $pagescripts = new h2o("{$_SERVER['DOCUMENT_ROOT']}/user-pages/scripts/onebooth-page.mst");
    $page->rawScript($pagescripts->render(array(
        "username" => $username,
        "boothnum" => $boothnum,
        "loggedIn" => isset($_SESSION['username'])
    )));
    $page->css($root."/css/posts.css");
    $page->css($root."/css/oneBooth-page.css");
    $page->css($root."/css/booth.css");
    $page->css($root."/css/textcomment-nocontext.css");
    $page->css($root."/css/textcomment-input.css");
    $page->useDefaultSideBars();
    $page->echoHtml();

}
