<?php
/**
 * Created by PhpStorm.
 * User: Brad
 * Date: 3/3/14
 * Time: 2:45 AM
 */

namespace comment;

require_once("{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php");
require_common("utils");
require_once("{$_SERVER['DOCUMENT_ROOT']}/comment/CommentObj.php");

class Comments {

    public static function loadForBooth($boothnumber) {

        $sql = "SELECT
					`fkOldestComment`, `fkNewestComment`
					FROM `boothcommentrangetbl`
					WHERE `fkBoothNumber` = ".$boothnumber."
					LIMIT 1;";
        $result0 = mysql_query($sql);
        if (!$result0) {
            go_to_db_error($sql);
            return;
        }
        if (mysql_num_rows($result0) == 0) {
            return;
        }
        $row = mysql_fetch_array($result0);

        $dtcol = "`datetime`";
        if (isset($_SESSION['time_zone'])) {
            $dtcol = "CONVERT_TZ(`datetime`,@@global.time_zone, '".$_SESSION['time_zone']."') as `datetime`";
        }
        $sql = "SELECT
					`pkCommentNumber`,
					`fkUsername`,
					`hasPhoto`,
					`imageHeightProp`,
					`commentBody`,
					`hash`,
					`extension`,
					".$dtcol.",
					HOUR( timediff( NOW( ) , `datetime` ) ) as `hours`,
					MINUTE( timediff( NOW( ) , `datetime` ) ) as `minutes`
					FROM `commentstbl`
					WHERE `fkNumber`=".$boothnumber."
					AND `pkCommentNumber` >= ".$row['fkOldestComment']."
					AND `pkCommentNumber` <= ".$row['fkNewestComment']."
					ORDER BY `pkCommentNumber` ASC
					;";
        $result1 = mysql_query($sql);

        $comments = array();
        if ($result1) {
            while ($row = mysql_fetch_array($result1)) {
                $comments[] = CommentObj::fromSQL($row);
            }
        } else {
            echo mysql_death1($sql);
        }
        return $comments;
    }

} 