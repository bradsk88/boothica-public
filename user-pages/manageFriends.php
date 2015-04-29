<?PHP

require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/framing/PageFrame.php";
require_page("LoginPage");

//TODO: Add 'remove friend' button

if (!isLoggedIn()) {
    $page = new LoginPage();
    echo $page->render();
    return;
}


$username = $_REQUEST['username'];
if (strlen($username) > 0 && strtolower($username) != strtolower($_SESSION['username'])) {
    header("Location: ".base()."/users/".$_SESSION['username']."/friends/manage");
    return;
}

$page = new PageFrame();
$page->setBodyTemplateAndValues("{$_SERVER['DOCUMENT_ROOT']}/user-pages/templates/manageFriends.mst");
$page->useDefaultSideBars();
$page->loadPublicSidebarsContent();
$page->css(base()."/css/posts.css");
$page->css(base()."/css/managefriends.css");
$page->css(base()."/css/bootherConsole.css");

$page->script(base()."/user-pages/scripts/manageFriends.js");
$page->script(base()."/user-pages/scripts/bootherConsole.js");
$page->rawScript("<script type = \"text/javascript\">
    $(document).ready(function() {
        loadIncomingFriendRequests(\"".$username."\");
        loadOutboundFriendRequests(\"".$username."\");
    });
</script>");

if (isLoggedIn() && $_SESSION['username'] == $username) {
    $page->rawScript("<script type = \"text/javascript\">
        $(document).ready(function() {
            loadOwnerConsole(\"".$username."\");
        });
        </script>");
} else {
    $page->rawScript("<script type = \"text/javascript\">
        $(document).ready(function() {
            loadBootherConsole(\"".$username."\");
        });
        </script>");
}

$page->echoHtml();
