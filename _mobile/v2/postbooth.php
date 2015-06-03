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

try {
    main();
} catch (Exception $e) {
    echo json_encode(array("error" => $e->getMessage()));
}

function main() {

    $dblink = connect_boothDB();
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

    if (parameterIsMissingAndEchoFailureMessage("requestHash")) {
        return;
    }
    $requestHash = $_POST['requestHash'];


    $dblink->autocommit(false);

    $sql = "SELECT requestHash FROM ( SELECT requestHash FROM `boothnumbers` ORDER BY datetime DESC LIMIT 1000 ) a WHERE requestHash = '".$requestHash."' LIMIT 2";
    $query = sql_query($sql);
    if (!$query) {
        echo json_encode(array("error" => sql_death1($sql)));
        return;
    }
    $dblink->autocommit(true);

    if ($query->num_rows > 0) {
        if ($query->num_rows == 2) {
            death("Multiple entries for booth request hash: ".$requestHash);
        }
        echo json_encode(array("error" => "It seems this booth has already been posted"));
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

    $result = doPostBooth($_SESSION['username'], $_POST['image'], $_POST['blurb'], $friendsonly, $requestHash);
    echo $result;
    return;

}