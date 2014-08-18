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
require_once "{$_SERVER['DOCUMENT_ROOT']}/common/utils.php";

class DisplayName {

    private $string = "ERR";

    public function __construct($username) {
        checkNotNull($username);
        $sql = "SELECT
			`displayname`
			FROM `logintbl`
			WHERE `username`='".$username."'
			LIMIT 1;";
        $result = mysql_query($sql);
        if ($result) {
            $row = mysql_fetch_array($result);
            $this->string = $row['displayname'];
        } else {
            mysql_death("getDisplayName() failed in utils_friend.php given value ".$username);
        }
    }

    public function __toString() {
        return $this->string;
    }

}