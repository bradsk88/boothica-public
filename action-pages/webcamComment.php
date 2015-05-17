<?PHP
require_once("{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/framing/PageFrame.php");
require_page("LoginPage");
require_page("ErrorPage");

if (!isLoggedIn()) {
    $page = new LoginPage();
    echo $page->render();
    return;
}

if (!isset($_REQUEST['boothnum'])) {
    $page = new ErrorPage("Missing parameter: boothnum");
    $page->echoHtml();
    return;
}

//TODO: File comment upload

$page = new PageFrame();
$page->setBodyTemplateAndValues("{$_SERVER['DOCUMENT_ROOT']}/newbooth/templates/webcam.mst", array(
    "headerText" => "Photo Comment",
    "postButtonText" => "Upload photo comment!",
    "specialClass" => "commentSnap"
));
$page->css(base()."/css/webcam.css");
$page->css(base()."/css/webcamcomment.css");
$page->script(base()."/action-pages/scripts/webcamComment.js");
$page->script(base()."/action-pages/scripts/webcamComment-page.js");
$page->script(base()."/lib/getUserMedia.js");
$page->rawScript("<script type = \"text/javascript\">
    window.boothNum = ".$_REQUEST['boothnum'].";
</script>");
$page->echoHtml();
return;
