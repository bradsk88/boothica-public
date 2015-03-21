<?php
/**
 * Created by PhpStorm.
 * User: Brad
 * Date: 3/17/15
 * Time: 9:11 PM
 */

require_once "{$_SERVER['DOCUMENT_ROOT']}/framing/PageFrame.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";

//TODO: Lower error level
error_reporting(0);
if (!isset($_SESSION['username'])) session_start();
main();

function main() {

    $root = base();

    $page = new PageFrame();

    if (!isset($_GET['boothername'])) {
        $errorHtml = new h2o("../framing/templates/error.mst");
        $html = $errorHtml->render(array(
            "shortError" => "Missing parameter boothername"

        ));
        $page->css($root."/css/error.css");
        $page->body($html);
    } else if (!isset($_GET['boothnum'])) {
        $errorHtml = new h2o("../framing/templates/error.mst");
        $html = $errorHtml->render(array(
            "shortError" => "Missing parameter boothnum"
        ));
        $page->css($root."/css/error.css");
        $page->body($html);
    } else {
        $pageHtml = new h2o("../framing/templates/textCommentInput.mst");
        $html = $pageHtml->render(array(
            "boothername" => $_GET['boothername'],
            "boothnum" => $_GET['boothnum']
        ));
        $page->body($html);
        $page->script($root."/comment/text-comment-scripts.js");
        $pagescripts = new h2o("../action-pages/comment-page-script.mst");
        $page->rawScript($pagescripts->render(array(
            "username" => $_GET['boothername'],
            "boothnum" => $_GET['boothnum'],
            "loggedIn" => isset($_SESSION['username'])
        )));
        $page->css($root."/css/textcomment-input.css");
    }


    $page->useDefaultSideBars($page);
    $page->echoHtml();

}