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
    $page->firstSideBar("New Friend Booths", false);
    $page->lastSideBar("New Public Booths");
    $page->script($root."/booth/user-booth-scripts.js");
    $page->script($root."/booth/user-booth-page-scripts.js");
    $page->css($root."/css/posts.css");
    $page->echoHtml();

}