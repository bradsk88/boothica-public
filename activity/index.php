<?php

session_start();

if (strpos(__FILE__, '_dev')) {
    require_once "{$_SERVER['DOCUMENT_ROOT']}/_dev/content/ContentPage.php";
} else {
    require_once "{$_SERVER['DOCUMENT_ROOT']}/content/ContentPage.php";
}

/**
 * @var $page ContentPage
 */
$page = new ContentPage("backToMain");
if (isset($_SESSION['username'])) {
    $page->meta("<script type = 'text/javascript'>username = '".$_SESSION['username']."'</script>");
}
//TODO: Load these scripts just-in-time -BJ
$page->meta("<script type = 'text/javascript' src = '/booth/booth-scripts.js.php'></script>");
$page->meta("<script type = 'text/javascript' src = '/booth/booth-comment-scripts.js'></script>");
$page->meta("<script type = 'text/javascript' src = '/booth/userbooths-scripts.js'></script>");
$page->meta("<script type = 'text/javascript' src = '/common/feed-scripts.js'></script>");
$page->meta("<script type = 'text/javascript' src = '/common/truncate.js'></script>");
$page->meta("<script type = 'text/javascript' src = '/activity/friendfeed-scripts.js'></script>");
$page->meta("<script type = 'text/javascript' src = '/activity/activity-scripts.js'></script>");
$page->meta("<script type = 'text/javascript' src = '/newbooth/newbooth-scripts.js'></script>");
$page->meta("<link rel='stylesheet' href='/css/activity.css' type='text/css' media='screen' />");
$page->meta("<link rel='stylesheet' href='/css/booth.css' type='text/css' media='screen' />");
$page->meta("<link rel = 'stylesheet' href = '/css/capture.css'  type='text/css' media='screen' />");
$page->meta("<link rel='stylesheet' href='/css/commentinput.css' type='text/css' media='screen' />");
$page->meta("<script type = 'text/javascript' src = '/common/jquery.a-tools-1.5.2.min.js'></script>");
$page->meta("<script type = 'text/javascript' src = '/common/jquery.asuggest.js'></script>");

$page->body("<div id = \"newmembers\" class = \"centersection\">This site requires JavaScript</div>
<div id = \"conversation\" class = \"centersection\"></div>");
$page->echoPage();
