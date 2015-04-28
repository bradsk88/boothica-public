<?PHP

require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/framing/PageFrame.php";
require_page("LoginPage");

//TODO: List outgoing requests
//TODO: Implement "cancel this outgoing request" action
//TODO: List ignored requests
//TODO: Implement "ignore this request" action
//TODO: Show "there are no requests" text

if (!isLoggedIn()) {
    $page = new LoginPage();
    echo $page->render();
    return;
}

$username = $_REQUEST['username'];
if (isset($username) && strtolower($username) != strtolower($_SESSION['username'])) {
    header("Location: ".base()."/users/".$_SESSION['username']."/friends/manage");
    return;
}

$page = new PageFrame();
$page->setBodyTemplateAndValues("{$_SERVER['DOCUMENT_ROOT']}/user-pages/templates/manageFriends.mst");
$page->useDefaultSideBars();
$page->loadPublicSidebarsContent();
$page->css(base()."/css/posts.css");
$page->css(base()."/css/managefriends.css");
$page->script(base()."/user-pages/scripts/manageFriends.js");
$page->rawScript("<script type = \"text/javascript\">
    $(document).ready(function() {
        loadIncomingFriendRequests(\"".$username."\");
    });
</script>");
$page->echoHtml();
