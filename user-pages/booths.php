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
    $page->script($root."/booth/user-booth-scripts.js");
    $pagescripts = new h2o("booths-page-script.mst");
    $page->rawScript($pagescripts->render(array(
        "username" => $_GET['username'],
        "loggedIn" => isset($_SESSION['username'])
    )));
    $page->css($root."/css/posts.css");
    $page->css($root."/css/posts-page.css");
    $page->css($root."/css/bootherConsole.css");
    $page->echoHtml();

}