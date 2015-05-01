<?PHP
require_once("{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/framing/PageFrame.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/pages/LoginPage.php");
require_lib("h2o-php/h2o");
require_common("internal_utils");
require_asset("UserImage");

class PrivateMessagePage extends PageFrame {

    function __construct() {
        parent::__construct();
        parent::setBodyTemplateAndValues("{$_SERVER['DOCUMENT_ROOT']}/pm/templates/index.mst", array(
            "baseUrl" => base(),
            "requestHash" => generateUserUniqueHash($_SESSION['username'])
        ));
    }

}

if (isLoggedIn()) {
    $page = new PrivateMessagePage();
    $page->css("http://fonts.googleapis.com/css?family=Bitter:400,700");
    $page->css(base()."/css/pm.css");
    $htmlBuilder = new h2o("{$_SERVER['DOCUMENT_ROOT']}/pm/templates/privateMessage.mst");
    $html = $htmlBuilder->render(array(
        "userImageUrl" => UserImage::getAbsoluteImage("roze")
    ));
    $page->body($html);
    $page->echoHtml();
} else {
    $page = new LoginPage();
    echo $page->render();
}
