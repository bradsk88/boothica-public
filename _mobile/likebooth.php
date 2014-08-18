<?php

require_once("{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/_mobile/utils.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/comment/CommentObj.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/comment/Comments.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/booth/utils.php");
require_common("utils");
require_common("internal_utils");

session_start();
error_reporting(0);
main();

function main() {

    $link = connect_to_boothsite();
    update_online_presence();

    $username = $_POST['username'];
    if (isset($_SESSION['username'])) {
        $username = $_SESSION['username'];
    } else {
        if (isset($_POST['username'])) {
            if (failsStandardMobileChecksAndEchoFailureMessage()) {
                return;
            }
            $_SESSION['username'] = $username;
        }
    }

    if (parameterIsMissingAndEchoFailureMessage("boothnum")) {
        return;
    }
    $boothnum = $_POST['boothnum'];

    if (!isAllowedToInteractWithBooth($username, $boothnum)) {
        echo "NoPermission:FAIL";
        return;
    }

    $sql = "REPLACE INTO `likes_boothstbl`
			(`fkBoothNumber`, `fkUsername`, `value`)
			VALUES
			('". $boothnum ."','".$username."', 1);";
    $result = mysql_query($sql);
    if (!$result) {
        echo mysql_death1($sql).":FAIL";
        return;
    }
    $sql = "SELECT
			COUNT(*) as `num`
			FROM `likes_boothstbl`
			WHERE `fkBoothNumber` = ".$boothnum.";";
    $numres = mysql_query($sql);
    if (!$numres) {
        echo mysql_death1($sql).":FAIL";
        return;
    }
    $row = mysql_fetch_array($numres);
    echo $row['num'].":OK";
    return;
}


