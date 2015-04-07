<?php

require_once "{$_SERVER['DOCUMENT_ROOT']}/framing/PageFrame.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";

error_reporting(E_ALL); //TODO: Adjust
if (!isset($_SESSION)) session_start();
main();

function main() {

    $root = base();
    $page = new PageFrame();
    $page->setBodyTemplateAndValues("templates/activity.mst", array());
    $page->css($root."/css/posts.css");
    $page->css($root."/css/activity.css");
    $page->css($root."/css/textcomment-withbooth.css");
    $page->useDefaultSideBars();
    $page->script(base()."/activity/script.js");
    $page->echoHtml();

}