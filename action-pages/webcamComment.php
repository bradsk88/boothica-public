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
$page->script(base()."/capture/webcam.js");
$page->script(base()."/capture/scripts/webcam.js");
$page->setBodyTemplateAndValues("{$_SERVER['DOCUMENT_ROOT']}/capture/templates/webcam.mst.html", array(
    "headerText" => "Photo Comment",
    "postButtonText" => "Upload photo comment!",
    "specialClass" => "commentSnap",
    "fileSupported" => false
));
$page->css(base()."/css/webcam.css");
$page->css(base()."/css/webcamcomment.css");
$page->script(base()."/action-pages/scripts/webcamComment-page.js");
$page->rawScript("<script type = \"text/javascript\">
    window.boothNum = ".$_REQUEST['boothnum'].";
</script>");
$page->echoHtml();
return;
