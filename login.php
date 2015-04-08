<?php
/**
 * Created by PhpStorm.
 * User: bradsk88
 * Date: 3/26/15
 * Time: 7:52 PM
 */

require_once "{$_SERVER['DOCUMENT_ROOT']}/framing/PageFrame.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";
require_lib("h2o-php/h2o");

$errorMessage = "";
if (isset($_REQUEST['wrongpass'])) {
    $errorMessage = "Password Incorrect - Try Again";
}

$username = null;
if (isset($_REQUEST['username'])) {
    $username = $_REQUEST['username'];
}

$action = new LoginPage($username, $errorMessage);
echo $action->render();

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

        $pageBuilder = new h2o("{$_SERVER['DOCUMENT_ROOT']}/framing/templates/login.mst");
        $root = base();
        $html = $pageBuilder->render(array(
            "baseUrl" => $root,
            "nextUrl" => $this->nextUrl,
            "errorMessage" => $this->errorMessage,
            "username" => $this->username,
            "promoteForgotPasswordButton" => isset($this->errorMessage)
        ));
        $page = new PageFrame();
        $page->body($html);
        $page->useDefaultSideBars();
        $page->loadPublicSidebarsContent();
        $page->css($root ."/css/login.css");
        $page->css($root ."/css/posts.css");
        $page->css("http://fonts.googleapis.com/css?family=Bitter:400,700");
        return $page->render();

    }

}