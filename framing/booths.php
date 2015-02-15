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
    $page->script($root."/booth/user-booth-scripts.js");
    $page->echoHtml();

}