<?php
/**
 * Created by PhpStorm.
 * User: Brad
 * Date: 4/25/14
 * Time: 9:44 PM
 */

require_once("{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php");
require_common("db"); require_common("utils");
require_once("{$_SERVER['DOCUMENT_ROOT']}/newbooth/post_utils.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/_mobile/utils.php");

error_reporting(0);
session_start();
main();

function main() {

    $link = connect_to_boothsite();
    $username = $_POST['username'];
    if (isset($_SESSION['username'])) {
        $username = $_SESSION['username'];
    } else if (isset($_POST['username']) && failsStandardMobileChecksAndEchoFailureMessage()) {
        return;
    }
    $_SESSION['username'] = $username;

    if (!userExists($username)) {
        echo json_encode(
            array(
                "error" => "Current user '" . $username . "' does not exist"));
        return;
    }

    if (isBanned($username)) {
        echo json_encode(
            array(
                "error" => "User is banned"));
        return;
    }

    if (parameterIsMissingAndEchoFailureMessage("image")) {
        return;
    }

    if (!isset($_POST["blurb"])) { # Can be empty
        echo json_encode(array('error' => 'Missing POST parameter: blurb'));
        return;
    }

    $friendsonly = false;
    if (isset($_POST['friendsonly'])) {
        $friendsonly = $_POST['friendsonly'];
    }

    list($code, ) = doPostBooth($_SESSION['username'], $_POST['image'], $_POST['blurb'], $friendsonly);
    if ($code == 0) {
        echo json_encode(array(
            'success' => 'booth posted successfully' 
        ));
    } else if ($code == 0214150354) {
        echo json_encode(array(
            'error' => 'Posting too rapidly.  Wait 5 minutes between posts.'
        ));
    } else {
        echo json_encode(array(
            'error' => 'the booth could not be posted' #TODO: Better errors
        ));
    }

    return;

}