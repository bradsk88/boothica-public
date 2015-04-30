<?PHP
require_once "{$_SERVER['DOCUMENT_ROOT']}/framing/PageFrame.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";

class InfoPage extends PageFrame {

    function __construct($title, $errorMessage="", $nextUrl=null, $nextUrlButtonText="Continue") {
        parent::__construct();
        parent::setBodyTemplateAndValues("{$_SERVER['DOCUMENT_ROOT']}/pages/templates/info.mst", array(
            "nextUrl" => $nextUrl ? $nextUrl : base(),
            "nextUrlButtonText" => $nextUrlButtonText,
            "errorMessage" => $errorMessage,
            "title" => $title
        ));
        parent::css("http://fonts.googleapis.com/css?family=Bitter:400,700");
        parent::excludeLoginNotification();
    }

}
