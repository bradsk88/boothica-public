<?PHP
require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";
require_page("ErrorPage");
require_page("LoginPage");

class PrivateMessageConversationPage extends PageFrame {

    function __construct($username) {
        parent::__construct();
        parent::setBodyTemplateAndValues("{$_SERVER['DOCUMENT_ROOT']}/action-pages/templates/pmConversation.mst", array(
            "userPossessiveDisplayName" => getPossessiveDisplayName($username),
            "userDisplayName" => getDisplayName($username),
            "username" => $username
        ));
    }

}


if (!isLoggedIn()) {
$page = new LoginPage();
echo $page->render();
return;
}

if (!isset($_REQUEST['username'])) {
    $page = new ErrorPage("Missing parameter: username");
    $page->echoHtml();
    return;
}

$page = new PrivateMessageConversationPage($_REQUEST['username']);
$page->script(base()."/action-pages/scripts/pmConversation.js");
$page->rawScript("
    <script type = \"text/javascript\">
        loadPMs(\"".$_REQUEST['username']."\");
    </script>
");
$page->echoHtml();
