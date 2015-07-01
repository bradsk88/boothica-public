<?php
/**
 * Created by PhpStorm.
 * User: bradsk88
 * Date: 3/26/15
 * Time: 7:52 PM
 */

error_reporting(0);

require_once "{$_SERVER['DOCUMENT_ROOT']}/framing/PageFrame.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";
require_lib("h2o-php/h2o");

$email = "";
if (isset($_REQUEST['email'])) {
    $email = $_REQUEST['email'];
}

$errorMessages = array();
if (isset($_REQUEST['errors'])) {
    $errorMessages = json_decode($_REQUEST['errors']);
}

$nextUrl = null;
if (isset($_REQUEST['nextUrl'])) {
    $nextUrl = $_REQUEST['nextUrl'];
}

$username = null;
if (isset($_REQUEST['username'])) {
    $username = $_REQUEST['username'];
}

$action = new RegistrationPage($username, $errorMessages, $email);
echo $action->render();

class RegistrationPage {

    private $nextUrl;
    private $errorMessages;
    private $username;
    private $email;

    function __construct($username=null, $errorMessages=array(), $email="", $nextUrl=null) {
        $this->nextUrl = $nextUrl or base();
        $this->errorMessages = $errorMessages;
        $this->username = $username;
        $this->email = $email;
    }

    function render() {

        $pageBuilder = new h2o("{$_SERVER['DOCUMENT_ROOT']}/framing/templates/registration.mst");
        $root = base();
        $html = $pageBuilder->render(array(
            "baseUrl" => $root,
            "username" => $this->username,
            "email" => $this->email,
            "errorMessages" => $this->errorMessages,
        ));
        $page = new PageFrame();
        $page->body($html);
        $page->useDefaultSideBars();
        $page->css($root ."/css/login.css");
        $page->css($root ."/css/registration.css");
        $page->css("http://fonts.googleapis.com/css?family=Bitter:400,700");
        $page->excludeLoginNotification();
        return $page->render();

    }

}