<?PHP

// This is an example of how to use InfoPage

require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";
require_once("{$_SERVER['DOCUMENT_ROOT']}/pages/InfoPage.php");
require_common("utils");

$page = new InfoPage("This is a test", "A long test message can go here and explain things to the user", base(),
                     "The button text can be custom");
$page->echoHtml();
