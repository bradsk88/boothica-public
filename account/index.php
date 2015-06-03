<?PHP

require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";
require_once("{$_SERVER['DOCUMENT_ROOT']}/framing/PageFrame.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/pages/LoginPage.php");
require_common("utils");

class AccountPage extends PageFrame {

    function __construct() {
        parent::__construct();
        $privacy = 2;
        if (isPublic($_SESSION['username'])) {
            $privacy = 0;
        }
        if (isSemiPublic($_SESSION['username'])) {
            $privacy = 1;
        }
        parent::setBodyTemplateAndValues("{$_SERVER['DOCUMENT_ROOT']}/account/templates/index.mst", array(
            "privacy" => $privacy
        ));
        parent::css(base()."/css/account.css");
    }

}

if (isLoggedIn()) {
    $page = new AccountPage();
    $page->echoHtml();
} else {
    $page = new LoginPage();
    echo $page->render();
}
