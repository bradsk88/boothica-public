<?php

error_reporting(0);

require("{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php");
require_common("db");
require_common("utils");
require_common("internal_utils");

$link = connect_to_boothsite();

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
                WHERE `fkUsername` = '".$username."' AND `fkPhoneID` = '".$phoneid."'
                LIMIT 1;";
                $keyres = mysql_query($sql);
                if (!$keyres) {
                    mysql_death1($sql);
                    echo json_encode(array("error"=>"Could not connect to database."));
                    return;
                }
                if (mysql_num_rows($keyres) == 1) {
                    $r = mysql_fetch_array($keyres);
                    echo json_encode(array("success" =>
                        array("key" => $r['key'])
                    ));
                    return;
                }
                echo json_encode(array("error"=>"No key"));
                return;

            } else {
                echo json_encode(array("error"=>"Bad login"));
                return;
        //		$response["success"] = 0;
        //		echo json_encode($response);

            }
            return;
        }
        echo json_encode(array("error"=>"Password missing"));
        return;
    } else {
        $rawArray = "POST";
        foreach ($_POST as $key => $value) {
            $rawArray .= $key . "=>" . $value;
        }
        $rawArray .= "GET";
        foreach ($_GET as $key => $value) {
            $rawArray .= $key . "=>" . $value;
        }
        echo json_encode(array("error"=>"Missing username or phoneid".$rawArray));
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
