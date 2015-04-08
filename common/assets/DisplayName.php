<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Brad
 * Date: 9/8/13
 * Time: 11:06 PM
 * To change this template use File | Settings | File Templates.
 */

/**
 * Class DisplayName
 * Requires that a database connection to the boothsite has been established.
 */
require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";
require_common("db");

class DisplayName {

    private $string = "ERR";

    public function __construct($username) {
        $dblink = connect_boothDB();
        $sql = "SELECT
			`displayname`
			FROM `logintbl`
			WHERE `username`='".$username."'
			LIMIT 1;";
        $result = $dblink->query($sql);
        if ($result and $result->num_rows == 1) {
            $row = $result->fetch_array();
            $this->string = $row['displayname'];
        } else {
            sql_death1("getDisplayName() failed in DisplayName.php given value ".$username);
        }
    }

    public function __toString() {
        return $this->string;
    }

}