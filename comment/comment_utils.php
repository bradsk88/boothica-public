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
    if ($results->num_rows > 0) {
        return true;
    }

    $sql = "SELECT fkUsername FROM boothnumbers
            WHERE pkNumber IN
                (SELECT fkNumber FROM `commentstbl`
                WHERE `pkCommentNumber` = '".$commentnumber."')
            LIMIT 1";

    $results = sql_query($sql);
    while ($row = $results->fetch_array()) {
        if (strtolower($row['fkUsername']) == strtolower($username)) {
            return true;
        }
    }

    return false;
}

function getCommentOwnerByNumber($commentNumber) {
    $sql = "SELECT `fkUsername` FROM `commentstbl` WHERE `pkCommentNumber` = '".$commentNumber."' LIMIT 1;";
    return sql_get_expectOneRow(sql_query($sql), "fkUsername");
}

function deleteCommentByNumber($commentNumber) {
    $sql = "SELECT `hash` FROM `commentstbl` WHERE `pkCommentNumber` = '".$commentNumber."' LIMIT 1;";
    $result = sql_query($sql);
    $hash = sql_get_expectOneRow($result, 'hash');
    if ($hash == null) {
        return array("error" => "No comment found with number ".$commentNumber);
    }
    $deleted = deleteCommentByHash($hash);
    return $deleted;
}

function getBoothNumberByComment($commentNumber) {
    $sql = "SELECT `fkNumber` FROM `commentstbl` WHERE `pkCommentNumber` = '".$commentNumber."' LIMIT 1;";
    $result = sql_query($sql);
    $boothNumber = sql_get_expectOneRow($result, 'fkNumber');
    return $boothNumber;
}

function deleteCommentByHash($hash) {
    $sql = "DELETE

				FROM `activitytbl`

				WHERE `fkIndex` IN (

					SELECT `pkCommentNumber`

					FROM `commentstbl`

					WHERE `hash` = '" . $hash . "');";

    $dblink = connect_boothDB();
    $result2 = $dblink->query($sql);

    if (!$result2) {

        return array(
            "error" => sql_death1($sql)
        );

    }


    $sql = "DELETE

				FROM `mentionstbl`

				WHERE `fkIndex` IN (

					SELECT `pkCommentNumber`

					FROM `commentstbl`

					WHERE `hash` = '" . $hash . "');";

    $result3 = $dblink->query($sql);

    if (!$result3) {

        return array(
            "error" => sql_death1($sql)
        );

    }


    $sql = "DELETE

				FROM `commentstbl`

				WHERE `hash` = '" . $hash . "';";

    $result = $dblink->query($sql);

    if (!$result) {

        return array(
            "error" => sql_death1($sql)
        );

    }

    return array(
        "success" => "Comment ".$hash." was deleted successfully"
    );
}
