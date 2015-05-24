<?PHP
require_once("{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/framing/PageFrame.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/pages/LoginPage.php");
require_lib("h2o-php/h2o");
require_common("internal_utils");
require_asset("UserImage");

class MentionsPage extends PageFrame {

    function __construct() {
        parent::__construct();
        parent::setBodyTemplateAndValues("{$_SERVER['DOCUMENT_ROOT']}/user-pages/templates/mentionsFrame.mst", array(
            "baseUrl" => base(),
        ));
    }

}

if (isLoggedIn()) {
    $page = new MentionsPage();
    $page->css("http://fonts.googleapis.com/css?family=Bitter:400,700");
    $page->css(base()."/css/error.css");
    $page->css(base()."/css/mentions.css");
    $page->css(base()."/css/textcomment-withbooth.css");
    $page->script(base()."/user-pages/scripts/mentionsScripts.js");
    $page->echoHtml();
} else {
    $page = new LoginPage();
    echo $page->render();
}

