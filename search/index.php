<?PHP

require_once "{$_SERVER['DOCUMENT_ROOT']}/framing/PageFrame.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";
require_lib("h2o-php/h2o");

include "{$_SERVER['DOCUMENT_ROOT']}/common/user_utils.php";

if (!isset($_SESSION['username'])) {
	return;
}

$htmlBuilder = new h2o("{$_SERVER['DOCUMENT_ROOT']}/search/templates/index.mst");
$html = $htmlBuilder->render(array(
    "baseUrl" => base()
));
$page = new PageFrame();
$page->body($html);
$page->useDefaultSideBars();
$page->echoHtml();
