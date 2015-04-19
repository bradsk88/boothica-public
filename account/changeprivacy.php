<?php

require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";
require_once("{$_SERVER['DOCUMENT_ROOT']}/framing/PageFrame.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/pages/LoginPage.php");
require_common("db");
require_common("utils");
require_lib("h2o-php/h2o");

class ChangePrivacyPage extends PageFrame {

    function __construct() {
        parent::__construct();
        parent::setBodyTemplateAndValues("{$_SERVER['DOCUMENT_ROOT']}/account/templates/changePrivacy.mst");
    }

}

if (isLoggedIn()) {
    $page = new ChangePrivacyPage();
    $page->echoHtml();
} else {
    $page = new LoginPage();
    echo $page->render();
}

