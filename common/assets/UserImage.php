<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Brad
 * Date: 9/11/13
 * Time: 2:45 PM
 * To change this template use File | Settings | File Templates.
 */

require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";
require_common("utils");
require_common("db");

class UserImage {

    private $string;

    const NOIMG = "/media/noimage.jpg";
    const PRIVATE_USER = "/media/private.jpg";

    public function __construct($username) {
        $this->string = UserImage::getImage($username);
    }
    public static function getImage($username) {

        $dblink = connect_boothDB();

        if (doesUserAppearPrivate($username)) {
            return UserImage::PRIVATE_USER;
        }

        $sql = "SELECT
                `imageTitle`,
                `filetype`
                FROM `boothnumbers`
                WHERE fkUsername = '".$username."'
                ORDER BY `pkNumber` DESC
                LIMIT 1;";
        $result = $dblink->query($sql);
        if (!$result) {
            sql_death1($sql);
            return UserImage::NOIMG;
        }
        if ($result->num_rows == 0) {
            return UserImage::NOIMG;
        } else {
            $row = $result->fetch_array();
            return  "/booths/tiny/".$row['imageTitle'].".".$row['filetype'];
        }

    }

    public static function getAbsoluteImage($username) {
        return base().UserImage::getImage($username);
    }

    public function __toString() {
        return $this->string;
    }
}
