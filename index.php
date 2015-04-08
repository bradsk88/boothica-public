<?php
/**
 * Created by PhpStorm.
 * User: Brad
 * Date: 3/16/15
 * Time: 9:00 PM
 * PHP version 5
 */
if (!isset($_SESSION)) session_start();
require_once "{$_SERVER['DOCUMENT_ROOT']}/framing/PageFrame.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";

if (isset($_SESSION['username'])) {
    header("Location: ".base()."/activity");
} else {
    if (!isset($_COOKIE['userid'])) {

            if (cookie_set() == 0) {
                $bodyOut .=  "Reloading. (This site requires JavaScript)";
                $bodyOut .=  "<script>parent.window.location.reload(true);</script>";
                return $bodyOut;
            } else {
                $page = new IndexPage();
                $page->render();
            }

    } else {
        $page = new IndexPage();
        $page->render();
    }
}

class IndexPage
{

    function render() 
    {

        $pageBuilder = new h2o("{$_SERVER['DOCUMENT_ROOT']}/framing/templates/login.mst");
        $root = base();
        $html = $pageBuilder->render(array(
            "baseUrl" => $root
        ));
        $page = new PageFrame();
        $page->body($html);
        $page->useDefaultSideBars();
        $page->css($root ."/css/posts.css");
        $page->css($root ."/css/login.css");
        $page->css("http://fonts.googleapis.com/css?family=Bitter:400,700");
        $page->loadPublicSidebarsContent();

        $page->echoHtml();

    }
}
