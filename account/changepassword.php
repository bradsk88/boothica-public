<?php

require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";
require_once("{$_SERVER['DOCUMENT_ROOT']}/framing/PageFrame.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/pages/LoginPage.php");
require_common("db");
require_common("utils");
require_lib("h2o-php/h2o");

class ChangePasswordPage extends PageFrame {

    function __construct($message = null) {
        parent::__construct();
        parent::setBodyTemplateAndValues("{$_SERVER['DOCUMENT_ROOT']}/account/templates/changePassword.mst",
            array(
                "message" => $message
            )
        );
    }

}

if (isLoggedIn()) {
    if (isset($_REQUEST['nomatch'])) {
        $page = new ChangePasswordPage("New passwords did not match");
    } else if (isset($_REQUEST['wrongpass'])) {
        $page = new ChangePasswordPage("The value provided for \"current password\" was wrong.");
    } else {
        $page = new ChangePasswordPage();
    }
    $page->echoHtml();
} else {
    $page = new LoginPage();
    echo $page->render();
}
