<?PHP
require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";
require_once("{$_SERVER['DOCUMENT_ROOT']}/pages/BannedUserPage.php");
require_common("utils");

$page = new BannedUserPage();
$page->echoHtml();
