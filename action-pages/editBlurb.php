<?PHP
require_once("{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/common/internal_utils.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/framing/PageFrame.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/pages/LoginPage.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/pages/ErrorPage.php");
require_asset("BoothImage");
require_lib("h2o-php/h2o");
require_common("db");

class EditBlurbPage extends PageFrame {

    function __construct() {
        parent::__construct();

        $sqlBuilder = new h2o("{$_SERVER['DOCUMENT_ROOT']}/action-pages/queries/getBlurb.mst.sql");
        $sql = $sqlBuilder->render(array(
            "boothNumber" => $_REQUEST['boothnum']
        ));

        $dblink = connect_boothDB();
        $request = $dblink->query($sql);
        $row = $request->fetch_array();
        $blurb = $row['blurb'];

        $htmlBuilder = new h2o("{$_SERVER['DOCUMENT_ROOT']}/action-pages/templates/editBlurb.mst");
        $html = $htmlBuilder->render(array(
            "baseUrl" => base(),
            "boothImageUrl" => BoothImage::getAbsoluteImageHiRes($_REQUEST['boothnum']),
            "blurb" => str_replace("<br />", "\n", $blurb),
            "bootherName" => getBoothOwner($_REQUEST['boothnum']),
            "boothNumber" => $_REQUEST['boothnum']
        ));
        $this->body($html);
    }

}

if (isLoggedIn()) {


    $owner = getBoothOwner($_REQUEST['boothnum']);
    if ($_SESSION['username'] != $owner) {
        $page = new ErrorPage("You don't own this booth", base()."/users/".$owner."/".$_REQUEST['boothnum'], "Back to ".getDisplayName($owner)."'s booth.");
        $page->echoHtml();
        return;
    }

    $page = new EditBlurbPage();
    $page->css(base()."/css/webcam.css");
    $page->css(base()."/css/posts.css");
    $page->css("http://fonts.googleapis.com/css?family=Bitter:400,700");
    $page->echoHtml();
} else {
    $page = new LoginPage();
    echo $page->render();
}
