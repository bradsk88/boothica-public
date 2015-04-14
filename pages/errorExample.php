<?PHP
require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";
require_once("{$_SERVER['DOCUMENT_ROOT']}/pages/ErrorPage.php");
require_common("utils");

$page = new ErrorPage("This is a test");
echo $page->render();