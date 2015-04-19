<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Brad
 * Date: 9/11/13
 * Time: 2:45 PM
 * To change this template use File | Settings | File Templates.
 */

require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/booth/utils.php";
require_common("utils");
require_common("db");

class BoothImage {

    const NOIMG = "/media/noimage.jpg";
    const PRIVATE_USER = "/media/private.jpg";

    public static function getImage($boothnum, $boothername=null) {

        $dblink = connect_boothDB();

        if ($boothername == null) {
            $boothername = getBoothOwner($boothnum);
        }

        if (doesUserAppearPrivate($boothername)) {
            return BoothImage::PRIVATE_USER;
        }

        $sql = "SELECT
                `imageTitle`,
                `filetype`
                FROM `boothnumbers`
                WHERE fkUsername = '".$boothername."'
                AND pkNumber = ".$boothnum."
                LIMIT 1;";
        $result = $dblink->query($sql);
        if (!$result) {
            sql_death1($sql);
            return BoothImage::NOIMG;
        }
        if ($result->num_rows == 0) {
            return BoothImage::NOIMG;
        } else {
            $row = $result->fetch_array();
            return  $row['imageTitle'].".".$row['filetype'];
        }

    }

    public static function getAbsoluteImage($boothnum, $boothername=null) {
        return base()."/booths/tiny/".BoothImage::getImage($boothnum, $boothername);
    }

    public static function getAbsoluteImageHiRes($boothnum, $boothername=null) {
        return base()."/booths/".BoothImage::getImage($boothnum, $boothername);
    }

}
