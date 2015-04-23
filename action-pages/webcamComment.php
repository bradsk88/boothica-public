<?PHP
require_once("{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/framing/PageFrame.php");

$page = new PageFrame();
$page->setBodyTemplateAndValues("{$_SERVER['DOCUMENT_ROOT']}/newbooth/templates/webcam.mst", array(
    "headerText" => "Photo Comment",
    "postButtonText" => "Upload photo comment!"
));
$page->css(base()."/css/webcam.css");
$page->script(base()."/action-pages/scripts/webcamComment.js");
$page->script(base()."/action-pages/scripts/webcamComment-page.js");
$page->script(base()."/lib/getUserMedia.js");
$page->rawScript("<script type = \"text/javascript\">
    window.boothNum = ".$_GET['boothnum'].";
</script>");
$page->echoHtml();
return;