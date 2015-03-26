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

$action = new RegistrationPage($username, $errorMessage);
echo $action->render();

class RegistrationPage {

    private $nextUrl;
    private $errorMessage;
    private $username;

    function __construct($username=null, $errorMessage="", $nextUrl=null) {
        $this->nextUrl = $nextUrl or base();
        $this->errorMessage = $errorMessage;
        $this->username = $username;
    }

    function render() {

        $pageBuilder = new h2o("{$_SERVER['DOCUMENT_ROOT']}/framing/templates/registration.mst");
        $root = base();
        $html = $pageBuilder->render(array(
            "baseUrl" => $root,
            "username" => $this->username,
        ));
        $page = new PageFrame();
        $page->body($html);
        $page->useDefaultSideBars();
        $page->css($root ."/css/login.css");
        $page->css($root ."/css/registration.css");
        $page->css("http://fonts.googleapis.com/css?family=Bitter:400,700");
        $page->script(base()."/lib/getUserMedia.js");
        $page->script(base()."/user-registration/script.js");
        return $page->render();

    }

}