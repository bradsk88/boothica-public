<?php
/**
 * Created by PhpStorm.
 * User: Brad
 * Date: 3/16/15
 * Time: 7:08 PM
 */

require_once "{$_SERVER['DOCUMENT_ROOT']}/framing/PageFrame.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";

main();

function main() {

    $root = base();

    $url = substr($_SERVER['REQUEST_URI'], 0, 32);
    if (strlen($_SERVER['REQUEST_URI']) > 32) {
        $url = $url."...";
    }

    $page = new PageFrame();
    $html= new h2o("404.mst");
    $page->body($html->render(array(
        "baseUrl" => $root,
        "requestUrl" => $root.$url
    )));
    $page->css($root."/css/posts.css");
    $page->css("http://fonts.googleapis.com/css?family=Bitter:400,700");
    $page->css($root."/css/404.css");
    $pagescripts = new h2o("404-script.mst");
    $page->rawScript($pagescripts->render(array(
        "loggedIn" => isset($_SESSION['username'])
    )));
    if (isset($_SESSION['username'])) {
        $page->firstSideBar("New Friend Booths", false);
    } else {
        $page->firstSideBar("Random Booths", false);
    }
    $page->echoHtml();

}