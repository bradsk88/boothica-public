<?php

session_start();

if (strpos(__FILE__, '_dev')) {
    require_once "{$_SERVER['DOCUMENT_ROOT']}/_dev/content/ContentPage.php";
} else {
    require_once "{$_SERVER['DOCUMENT_ROOT']}/content/ContentPage.php";
}

$base = base();

/**
 * @var $page ContentPage
 */
$page = new ContentPage("backToMain");
if (isset($_SESSION['username'])) {
    $page->meta("<script type = 'text/javascript'>username = '".$_SESSION['username']."'</script>");
}
//TODO: Load these scripts just-in-time -BJ
$page->meta("<script type = 'text/javascript' src = '".$base."/booth/booth-scripts.js'></script>");
$page->meta("<script type = 'text/javascript' src = '".$base."/booth/booth-comment-scripts.js'></script>");
$page->meta("<script type = 'text/javascript' src = '".$base."/booth/userbooths-scripts.js'></script>");
$page->meta("<script type = 'text/javascript' src = '".$base."/common/feed-scripts.js'></script>");
$page->meta("<script type = 'text/javascript' src = '".$base."/common/truncate.js'></script>");
$page->meta("<script type = 'text/javascript' src = '".$base."/activity/friendfeed-scripts.js'></script>");
$page->meta("<script type = 'text/javascript' src = '".$base."/activity/activity-scripts.js'></script>");
$page->meta("<script type = 'text/javascript' src = '".$base."/newbooth/newbooth-scripts.js'></script>");
$page->meta("<link rel='stylesheet' href='".$base."/css/activity.css' type='text/css' media='screen' />");
$page->meta("<link rel='stylesheet' href='".$base."/css/booth.css' type='text/css' media='screen' />");
$page->meta("<link rel = 'stylesheet' href = '".$base."/css/capture.css'  type='text/css' media='screen' />");
$page->meta("<link rel='stylesheet' href='".$base."/css/commentinput.css' type='text/css' media='screen' />");
$page->meta("<script type = 'text/javascript' src = '".$base."/common/jquery.a-tools-1.5.2.min.js'></script>");
$page->meta("<script type = 'text/javascript' src = '".$base."/common/jquery.asuggest.js'></script>");

$page->body("<div id = \"newmembers\" class = \"centersection\">This site requires JavaScript</div>
<div id = \"conversation\" class = \"centersection\"></div>");
$page->echoPage();
