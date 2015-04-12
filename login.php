<?php
/**
 * Created by PhpStorm.
 * User: bradsk88
 * Date: 3/26/15
 * Time: 7:52 PM
 */

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
