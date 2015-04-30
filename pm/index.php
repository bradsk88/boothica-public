<?PHP
require_once("{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/framing/PageFrame.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/pages/LoginPage.php");
require_lib("h2o-php/h2o");
require_common("internal_utils");

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
    $page->echoHtml();
} else {
    $page = new LoginPage();
    echo $page->render();
}
