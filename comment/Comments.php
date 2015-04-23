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
require_common("db");
require_once("{$_SERVER['DOCUMENT_ROOT']}/comment/CommentObj.php");

class Comments {

    public static function loadForBooth($boothnumber) {

        $dblink = connect_boothDB();
        $sql = "SELECT
					`fkOldestComment`, `fkNewestComment`
					FROM `boothcommentrangetbl`
					WHERE `fkBoothNumber` = ".$boothnumber."
					LIMIT 1;";
        $result0 = $dblink->query($sql);

        if (!$result0) {
            return array("error" => sql_death1($sql));
        }
        if ($result0->num_rows == 0) {
            return array(
                "success" => array()
            );
        }

        $row = $result0->fetch_array();

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

        $result1 = $dblink->query($sql);

        $comments = array();

        if ($result1) {
            while($row = $result1->fetch_array()) {
                try {
                    $comments[] = CommentObj::fromSQL($row);
                } catch (\Exception $e) {
                    death("Unable to parse comment from row: .".$row);
                    continue;
                }
            }
        } else {
            return array("error" => sql_death1($sql));
        }
        return array("success" => $comments);
    }

} 
