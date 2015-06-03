<?PHP
require_once "{$_SERVER['DOCUMENT_ROOT']}/framing/PageFrame.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";
require_lib("h2o-php/h2o");

class BannedUserPage extends PageFrame {

    function __construct() {
        parent::__construct();
        parent::setBodyTemplateAndValues("{$_SERVER['DOCUMENT_ROOT']}/pages/templates/error.mst", array(
            "nextUrl" => base(),
            "nextUrlButtonText" => "Back to ".basePretty(),
            "errorMessage" => "You account is banned",
        ));
        parent::css("http://fonts.googleapis.com/css?family=Bitter:400,700");
    }

}
