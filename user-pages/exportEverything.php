<?PHP
require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/pages/InfoPage.php";

$page = new InfoPage("The End", "This page will, eventually, supply the ability to export your booths.
<br/><br/>Stay tuned.", base().'/user-pages/doExportEverything');
$page->echoHtml();
