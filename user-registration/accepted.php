<?PHP
require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/pages/InfoPage.php";

$page = new InfoPage("Accepted", "Check your email for a confirmation.<br/><br/>(Check spam too!)", base(), "Back to Boothi.ca");
$page->echoHtml();
