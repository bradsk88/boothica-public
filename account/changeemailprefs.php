<?php

require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";
require_once("{$_SERVER['DOCUMENT_ROOT']}/framing/PageFrame.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/pages/LoginPage.php");
require_common("db");
require_common("utils");
require_lib("h2o-php/h2o");

class ChangeEmailPrefsPage extends PageFrame {

    function __construct() {
        parent::__construct();
        $dblink = connect_boothDB();
        $sqlBuilder = new h2o("{$_SERVER['DOCUMENT_ROOT']}/account/queries/getEmailPrefs.mst.sql");
        $sql = $sqlBuilder->render(array(
            "username" => $dblink->real_escape_string($_SESSION['username'])
        ));
        $result = $dblink->query($sql);
        $row = $result->fetch_array();
        parent::setBodyTemplateAndValues("{$_SERVER['DOCUMENT_ROOT']}/account/templates/changeEmailPrefs.mst", array(
            "fromMods" => (bool)($row['fromMods']),
            "newPM" => (bool)($row['newPM']),
            "friendBooth" => (bool)($row['friendBooth']),
            "boothComment" => (bool)($row['boothComment']),
            "mention" => (bool)($row['mention']),
            "friendRequest" => (bool)($row['friendRequest']),
            "friendAccept" => (bool)($row['friendAccept']),
            "announcements" => (bool)($row['announcements']),
        ));
        $this->notificationRegion = "<div class = \"messageBanner\">Click submit at the bottom to register changes</div>";
        parent::css(base()."/css/account.css");
        parent::css(base()."/css/controls.css");
    }

}

if (isLoggedIn()) {
    $page = new ChangeEmailPrefsPage();
    $page->echoHtml();
} else {
    $page = new LoginPage();
    echo $page->render();
}

