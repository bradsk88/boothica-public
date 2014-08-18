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

    $sql = "SELECT COUNT(*) as `num`
		FROM `friendstbl` f
			WHERE f.`fkUsername` NOT IN (
				SELECT `fkFriendName`
					FROM `friendstbl`
						WHERE `fkUsername` = f.`fkFriendName`)
						AND f.`fkFriendName` = '".$username."'
						AND `ignored` = 0;";
    $result = sql_query($sql);
    $num = sql_get_expectOneRow($result, "num");
    echo $num;
}