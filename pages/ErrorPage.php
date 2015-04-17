<?PHP
require_once "{$_SERVER['DOCUMENT_ROOT']}/framing/PageFrame.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";
require_lib("h2o-php/h2o");

class ErrorPage extends PageFrame {

    function __construct($errorMessage="", $nextUrl=null, $nextUrlButtonText="Try again later") {
        parent::__construct();
        parent::setBodyTemplateAndValues("{$_SERVER['DOCUMENT_ROOT']}/pages/templates/error.mst", array(
            "nextUrl" => $nextUrl,
            "nextUrlButtonText" => $nextUrlButtonText,
            "errorMessage" => $errorMessage,
        ));
        parent::css("http://fonts.googleapis.com/css?family=Bitter:400,700");
    }

}
