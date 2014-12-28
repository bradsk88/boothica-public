<?php

error_reporting(0);

require("{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php");
require_common("db");
require_common("utils");
require_common("internal_utils");
$link = connect_to_boothsite();
update_online_presence();
mobileLogin();

function mobileLogin() {
    if (isset($_POST['username']) && isset($_POST['phoneid'])) {

        $username = mysql_real_escape_string(strtolower($_POST['username']));
        if (isset($_POST['pass'])) {
            $sql = '
              SELECT
                `password`, `attempts`, `restorecode`
              FROM `logintbl`
                WHERE
                  `username` = "' . $username . '"
              LIMIT 1
              ;';

            $r = mysql_fetch_assoc(mysql_query($sql));

            // The first 64 characters of the hash is the salt
            $salt = substr($r['password'], 0, 64);
            $hash = generateSaltedHash($salt, $_POST['pass']);

            if ( $hash == $r['password'] ) {

                $phoneid = $_POST['phoneid'];

                if (!insertNewKey($username, $phoneid)) {
                    return;
                }

                $sql = "
                SELECT
                    `key`
                FROM `phonekeystbl`
                WHERE `fkUsername` = '".$username."' AND `fkPhoneID` = ".$phoneid."
                LIMIT 1;";
                $keyres = mysql_query($sql);
                if (!$keyres) {
                    mysql_death1($sql);
                    echo "Could not connect to database.";
                    return;
                }
                if (mysql_num_rows($keyres) == 1) {
                    $r = mysql_fetch_array($keyres);
                    echo "KEY:".$r['key'];
                    return;
                }
                echo "No key";
                return;

            } else {
                echo "Bad login";
                return;
        //		$response["success"] = 0;
        //		echo json_encode($response);

            }
            return;
        }
        echo "Password missing";
        return;
    }
}

function insertNewKey($username, $phoneid) {
    $key = generateRandomString();
    $sql = "
                    REPLACE INTO
                    `phonekeystbl`
                    (`fkUsername`, `fkPhoneID`, `key`)
                    VALUES
                    ('".$username."','".$phoneid."','".$key."');";
    $keyres = mysql_query($sql);
    if (!$keyres) {
        echo mysql_death1($sql);
        return false;
    }
    return true;
}
