<?php

require_once "{$_SERVER['DOCUMENT_ROOT']}/framing/PageFrame.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/pages/LoginPage.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";
require_lib("h2o-php/h2o");

error_reporting(0);
if (!isset($_SESSION)) session_start();
main();

function main() {

    if (isLoggedIn()) {
        $page = new PageFrame();
        $page->useDefaultSideBars();
        $page->script(base()."/activity/script.js");
        $root = base();
        $page->setBodyTemplateAndValues("templates/activity.mst", array());
        $page->css($root."/css/posts.css");
        $page->css($root."/css/activity.css");
        $page->css($root."/css/textcomment-withbooth.css");
        $page->css($root."/css/photocomment-withbooth.css");
        $nonCacheableScriptBuilder = new h2o("{$_SERVER['DOCUMENT_ROOT']}/activity/templates/activityscripts-noncacheable.mst");
        $page->rawScript($nonCacheableScriptBuilder->render());
        $page->echoHtml();
    } else {
        $page = new LoginPage();
        echo $page->render();
    }

}