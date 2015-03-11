<?php
/**
 * Utility functions for mobile api
 */
define(NO_KEY, -2);
define(BAD, -1);
define(OK, 0);

require_once("{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php");
require_common("utils");
require_common("internal_utils");

function failsStandardMobileChecksAndEchoFailureMessage() {

    if (parameterIsMissingAndEchoFailureMessage('username')) {
        return true;
    }
    if (parameterIsMissingAndEchoFailureMessage('phoneid')) {
        return true;
    }
    if (parameterIsMissingAndEchoFailureMessage('loginkey')) {
        return true;
    }
    $check = isKeyOK($_POST['username'], $_POST['phoneid'], $_POST['loginkey']);

    if ($check == OK) {
        return false;
    }

    if ($check == BAD) {
        echo json_encode(
            array('error' => "Login key not accepted")
        );
        return true;
    }
    if ($check == NO_KEY) {
        echo json_encode(
            array('error' => "Login key was missing")
        );
        return true;
    }

    echo json_encode(
        array('error' => "Unexpected error")
    );
    return true;

}

function isKeyOK($username, $phoneid, $loginkey) {

    $link = connect_mysqli_to_boothsite();
    $username = $link->escape_string(strtolower($username));
    $phoneid = $link->escape_string($phoneid);
    if (isset($_POST['loginkey'])) {
        $sql = "
              SELECT
                `key`
              FROM `phonekeystbl`
                WHERE
                  `fkUsername` = '" . $username . "' AND `fkPhoneID` = '".$phoneid."'
              LIMIT 1;";

        $res = sql_query($sql);
        if (!$res) {
            mysql_death1($sql);
            return BAD;
        }
        $r = $res->fetch_assoc();

        if ($r['key'] == $loginkey) {
            return OK;
        }
        return BAD;
    }
    return NO_KEY;
}