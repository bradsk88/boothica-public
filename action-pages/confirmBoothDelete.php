<?PHP
require_once("{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/common/internal_utils.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/booth/utils.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/framing/PageFrame.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/pages/LoginPage.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/pages/ErrorPage.php");
require_lib("h2o-php/h2o");

class ConfirmDeleteBoothPage extends PageFrame {

    function __construct($owner) {
        parent::__construct();

        $this->setBodyTemplateAndValues("{$_SERVER['DOCUMENT_ROOT']}/action-pages/templates/confirmBoothDelete.mst", array(
            "baseUrl" => base(),
            "boothNumber" => $_REQUEST['boothnum'],
            "bootherName" => $owner
        ));
    }

}

if (isLoggedIn()) {


    $owner = getBoothOwner($_REQUEST['boothnum']);
    if ($_SESSION['username'] != $owner) {
        $page = new ErrorPage("You don't own this booth", base()."/users/".$owner."/".$_REQUEST['boothnum'], "Back to ".getDisplayName($owner)."'s booth.");
        $page->echoHtml();
        return;
    }
    $page = new ConfirmDeleteBoothPage($owner);
    $page->css(base()."/css/webcam.css");
    $page->css(base()."/css/posts.css");
    $page->css("http://fonts.googleapis.com/css?family=Bitter:400,700");
    $page->useDefaultSideBars();
    $page->echoHtml();
} else {
    $page = new LoginPage();
    echo $page->render();
}
