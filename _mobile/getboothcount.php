<?php
/**
 * Created by PhpStorm.
 * User: Brad
 * Date: 5/25/14
 * Time: 10:49 PM
 */

session_start();
error_reporting(0);
main();

function main() {

    require_once("{$_SERVER['DOCUMENT_ROOT']}/_mobile/utils.php");
    require_once("{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php");
    require_common("db"); require_common("utils");

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

    if (!userExists($username)) {
        echo -1;
    }

    $sql = "SELECT count(*) as `num` FROM `boothnumbers` WHERE `fkUsername` = '".$username."';";

    $result = sql_query($sql);
    if (!$result) {
        echo
            json_encode(
                array(
                    "error" => mysql_death1($sql)
                )
            );
        return;
    }
    if (emptyResult($result)) {
        echo -1;
        return;
    }
    $num = sql_get_expectOneRow($result, "num");
    if ($num == 0) {
        echo -1;
        return;
    }
    echo $num; //there were notifications
    return;
}