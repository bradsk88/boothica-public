<?PHP
require_once("{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/common/internal_utils.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/booth/utils.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/comment/comment_utils.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/framing/PageFrame.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/pages/LoginPage.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/pages/ErrorPage.php");
require_lib("h2o-php/h2o");

class ConfirmDeleteCommentPage extends PageFrame {

    function __construct($commentNumber, $nextUrl) {
        parent::__construct();

        $this->setBodyTemplateAndValues("{$_SERVER['DOCUMENT_ROOT']}/action-pages/templates/confirmCommentDelete.mst", array(
            "baseUrl" => base(),
            "commentNumber" => $commentNumber,
            "nextUrl" => $nextUrl
        ));
    }

}

if (isLoggedIn()) {
    $commentNumber = $_REQUEST['commentnumber'];
    $commentOwner = getCommentOwnerByNumber($commentNumber);
    $nextUrl = $_REQUEST['nextUrl'];

    if ($_SESSION['username'] != $commentOwner) {
        $page = new ErrorPage("You don't own this comment", $nextUrl, "Back to booth.");
        $page->echoHtml();
        return;
    }

    $page = new ConfirmDeleteCommentPage($commentNumber, $nextUrl);
    $page->css("http://fonts.googleapis.com/css?family=Bitter:400,700");
    $page->echoHtml();
} else {
    $page = new LoginPage();
    echo $page->render();
}
