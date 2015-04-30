<?PHP		
	require_once("{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php");
    require_once("{$_SERVER['DOCUMENT_ROOT']}/common/internal_utils.php");
    require_once("{$_SERVER['DOCUMENT_ROOT']}/framing/PageFrame.php");
    require_once("{$_SERVER['DOCUMENT_ROOT']}/pages/LoginPage.php");
    require_lib("h2o-php/h2o");

    class WebcamPage extends PageFrame {

        function __construct() {
            parent::__construct();
            $htmlBuilder = new h2o("{$_SERVER['DOCUMENT_ROOT']}/newbooth/templates/webcam.mst");
            $html = $htmlBuilder->render(array(
                "baseUrl" => base(),
                "headerText" => "New Booth",
                "postButtonText" => "Post this booth!",
            ));
            $this->body($html);
        }

    }

    if (isLoggedIn()) {
        $page = new WebcamPage();
        $page->script(base()."/newbooth/webcam.js");
        $page->script(base()."/lib/getUserMedia.js");
        $pageScriptBuilder = new h2o("{$_SERVER['DOCUMENT_ROOT']}/newbooth/scripts/webcam-page.mst");
        $page->rawScript($pageScriptBuilder->render(array()));
        $page->rawScript("<script type = \"text/javascript\">
            $(document).ready(function() {
                window.requestHash = \"".generateUserUniqueHash($_SESSION['username'])."\";
            });
        </script>");

        $page->css(base()."/css/webcam.css");
        $page->css(base()."/css/posts.css");
        $page->css("http://fonts.googleapis.com/css?family=Bitter:400,700");
        $page->useDefaultSideBars();
        $page->echoHtml();
    } else {
        $page = new LoginPage();
        echo $page->render();
    }
