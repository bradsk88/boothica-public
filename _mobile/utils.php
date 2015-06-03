<?php
/**
 * Utility functions for mobile api
 */

class MobileConstants {
    const NO_KEY = -2;
    const BAD = -1;
    const OK = 0;
}

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

    if ($check == MobileConstants::OK) {
        return false;
    }

    if ($check == MobileConstants::BAD) {
        echo json_encode(
            array('error' => "Login key not accepted")
        );
        return true;
    }
    if ($check == MobileConstants::NO_KEY) {
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

    $dblink = connect_boothDB();
    $username = $dblink->escape_string(strtolower($username));
    $phoneid = $dblink->escape_string($phoneid);
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
            sql_death1($sql);
            return MobileConstants::BAD;
        }
        $r = $res->fetch_assoc();

        if ($r['key'] == $loginkey) {
            return MobileConstants::OK;
        }
        return MobileConstants::BAD;
    }
    return MobileConstants::NO_KEY;
}
