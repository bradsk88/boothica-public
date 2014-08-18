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

    public function __construct($username) {
        $this->string = UserImage::getImage($username);
    }
    public static function getImage($username) {




        if (isset($_SESSION['username']) && (isFriendOf($_SESSION['username'], $username))) {
            $sql = "SELECT
                    `imageTitle`,
                    `filetype`
                    FROM `boothnumbers`
                    WHERE fkUsername = '".$username."'
                    ORDER BY `pkNumber` DESC
                    LIMIT 1;";
            $result = mysql_query($sql);
            if (!$result) {
                mysql_death1($sql);
                return;
            }
            if (mysql_num_rows($result) == 0) {
                return UserImage::NOIMG;
            } else {
                $row = mysql_fetch_array($result);
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
            $result = mysql_query($sql);
            if (!$result) {
                mysql_death1($sql);
                return;
            }
            if (mysql_num_rows($result) == 0) {
                return UserImage::NOIMG;
            } else {
                $row = mysql_fetch_array($result);
                return  "/booths/tiny/".$row['imageTitle'].".".$row['filetype'];
            }
        } else {
            $sql = "SELECT
				`hasIcon`,
				`iconext`
				FROM `logintbl`
				WHERE `username` = '".$username."'
				LIMIT 2;";
            $result = mysql_query($sql);
            if (!$result) {
                mysql_death1($sql);
                return UserImage::NOIMG;
            }

            $num = mysql_num_rows($result);
            if ($num == 1) {
                $row = mysql_fetch_array($result);
                if ($row['hasIcon'] == 1) {
                    return  "/users/".$username."/public.".$row['iconext'];
                } else {
                    return "/media/private.jpg";
                }
            } else {
                death($num." rows in logintbl for user: ".$username);
                return UserImage::NOIMG;
            }
        }

    }

    public function __toString() {
        return $this->string;
    }
}