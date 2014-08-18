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
main();

function main() {

    $link = connect_to_boothsite();
    if (failsStandardMobileChecksAndEchoFailureMessage()) {
        return;
    }

    $_SESSION['username'] = $_POST['username'];

    $friendsonly = false;
    if (isset($_POST['friendsonly'])) {
        $friendsonly = $_POST['friendsonly'];
    }

    list($code, ) = doPostBooth($_POST['username'], $_POST['image'], $_POST['blurb'], $friendsonly);
    echo $code;

    unset($_SESSION['username']);
    return;

}