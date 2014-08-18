<?php
/**
 * Created by PhpStorm.
 * User: Brad
 * Date: 5/25/14
 * Time: 1:13 PM
 */

require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";
require_common("utils"); require_common("db");

function isAllowedToDeleteCommentNumber($username, $commentnumber) {

    if (isModerator($username)) {
        return true;
    }

    $sql = "SELECT true
            FROM `commentstbl`
            WHERE `pkCommentNumber` = '".$commentnumber."'
            AND `fkUsername` = '".$username."';";

    $results = sql_query($sql);
    if (emptyResult($results)) {
        return false;
    }

    return true;

}

function getCommentOwnerByNumber($commentNumber) {
    $sql = "SELECT `fkUsername` FROM `commentstbl` WHERE `pkCommentNumber` = '".$commentNumber."' LIMIT 1;";
    return sql_get_expectOneRow(sql_query($sql), "fkUsername");
}

function deleteCommentByNumber($commentNumber, $link, $username) {
    $sql = "SELECT `hash` FROM `commentstbl` WHERE `pkCommentNumber` = '".$commentNumber."' LIMIT 1;";
    $result = sql_query($sql);
    $hash = sql_get_expectOneRow($result, 'hash');
    if ($hash == null) {
        return false;
    }
    deleteCommentByHash($hash, $link, $username);
    return true;
}


function deleteCommentByHash($hash, $link, $username) {
    $sql = "DELETE

				FROM `activitytbl`

				WHERE `fkIndex` IN (

					SELECT `pkCommentNumber`

					FROM `commentstbl`

					WHERE `hash` = '" . $hash . "');";

    $result2 = mysql_query($sql);

    if (!$result2) {

        return mysql_death(mysql_error($link) . "\n" . $sql);

    }


    $sql = "DELETE

				FROM `mentionstbl`

				WHERE `fkIndex` IN (

					SELECT `pkCommentNumber`

					FROM `commentstbl`

					WHERE `hash` = '" . $hash . "');";

    $result3 = mysql_query($sql);

    if (!$result3) {

        return mysql_death1($sql);

    }


    $sql = "DELETE

				FROM `commentstbl`

				WHERE `hash` = '" . $hash . "'

				AND (

					`fkUsername` = '" . $username . "'

					OR

					`fkNumber` IN (

						SELECT `pkNumber`

						FROM `boothnumbers`

						WHERE `fkUsername` = '" . $username . "'

					)

				);";

    $result = mysql_query($sql);

    if (!$result) {

        return mysql_death(mysql_error($link) . "\n" . $sql);

    }

    return 0;
}