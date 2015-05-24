<?PHP
require_once "{$_SERVER['DOCUMENT_ROOT']}/framing/PageFrame.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";
require_lib("h2o-php/h2o");

class LoginPage {

    private $nextUrl;
    private $errorMessage;
    private $username;

    function __construct($username=null, $errorMessage="", $nextUrl=null) {
        $this->nextUrl = $nextUrl or base();
        $this->errorMessage = $errorMessage;
        $this->username = $username;
    }

    function render() {

        $page = new PageFrame(true);
        $page->css(base() ."/css/login.css");
        $page->css(base() ."/css/posts.css");
        $page->css("http://fonts.googleapis.com/css?family=Bitter:400,700");
        $page->setBodyTemplateAndValues("{$_SERVER['DOCUMENT_ROOT']}/framing/templates/login.mst",
        array(
            "nextUrl" => $this->nextUrl,
            "errorMessage" => $this->errorMessage,
            "username" => $this->username,
            "promoteForgotPasswordButton" => isset($this->errorMessage)
        ));
        $page->excludeLoginNotification();
        return $page->render();

    }

}
