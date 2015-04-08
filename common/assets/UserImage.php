<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Brad
 * Date: 9/11/13
 * Time: 2:45 PM
 * To change this template use File | Settings | File Templates.
 */

class UserImage {

    private $string;

    const NOIMG = "/media/noimage.jpg";
    const PRIVATE_USER = "/media/private.jpg";

    public function __construct($username) {
        $this->string = UserImage::getImage($username);
    }
    public static function getImage($username) {

        $dblink = connect_boothDB();

        if (isset($_SESSION['username']) && (isFriendOf($_SESSION['username'], $username))) {
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
                return null;
            }
            if ($result->num_rows == 0) {
                return UserImage::NOIMG;
            } else {
                $row = $result->fetch_array();
                return  "/booths/tiny/".$row['imageTitle'].".".$row['filetype'];
            }
        } else if(isPublic($username)) {

            $sql = "SELECT
                    `imageTitle`,
                    `filetype`
                    FROM `boothnumbers`
                    WHERE fkUsername = '".$username."'
                    AND `isPublic` = true
                    ORDER BY `pkNumber` DESC
                    LIMIT 1;";
            $result = $dblink->query($sql);
            if (!$result) {
                sql_death1($sql);
                return null;
            }
            if ($result->num_rows == 0) {
                return UserImage::NOIMG;
            } else {
                $row = $result->fetch_array();
                return  "/booths/tiny/".$row['imageTitle'].".".$row['filetype'];
            }
        } else {
            $sql = "SELECT
				`hasIcon`,
				`iconext`
				FROM `logintbl`
				WHERE `username` = '".$username."'
				LIMIT 2;";
            $result = $dblink->query($sql);
            if (!$result) {
                sql_death1($sql);
                return UserImage::NOIMG;
            }

            $num = $result->num_rows;
            if ($num == 1) {
                $row = $result->fetch_array();
                if ($row['hasIcon'] == 1) {
                    return  "/users/".$username."/public.".$row['iconext'];
                } else {
                    return UserImage::PRIVATE_USER;
                }
            } else {
                death($num." rows in logintbl for user: ".$username);
                return UserImage::NOIMG;
            }
        }

    }

    public static function getAbsoluteImage($username) {
        return base().UserImage::getImage($username);
    }

    public function __toString() {
        return $this->string;
    }
}