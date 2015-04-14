<?php

require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";
require_once("{$_SERVER['DOCUMENT_ROOT']}/framing/PageFrame.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/pages/LoginPage.php");
require_common("db");
require_common("utils");
require_lib("h2o-php/h2o");

class ChangeEmailPage extends PageFrame {

    function __construct($message = null) {
        parent::__construct();
        parent::setBodyTemplateAndValues("{$_SERVER['DOCUMENT_ROOT']}/account/templates/changeEmail.mst",
            array(
                "message" => $message
            )
        );
    }

}

if (isLoggedIn()) {
    if (isset($_REQUEST['nomatch'])) {
        $page = new ChangeEmailPage("New email addresses did not match");
    } else if (isset($_REQUEST['badformat'])) {
        $page = new ChangeEmailPage("Bad email address format detected");
    } else {
        $page = new ChangeEmailPage();
    }
    $page->echoHtml();
} else {
    $page = new LoginPage();
    echo $page->render();
}
