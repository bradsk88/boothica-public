<?php
/**
 * Created by PhpStorm.
 * User: Brad
 * Date: 5/6/14
 * Time: 11:09 PM
 */
session_start();
error_reporting(0);

require_once("{$_SERVER['DOCUMENT_ROOT']}/_mobile/utils.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/mentions/mentions_utils.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/mentions/MentionBoothInfo.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php");

require_common("user_utils");
require_common("db");
require_common("utils");

try {
    main();
} catch (Exception $e) {
    death($e);
    echo json_encode(array("error"=>"Fatal Error"));
}

function main()
{

    $link = connect_to_boothsite();
    update_online_presence();


    $username = $_POST['username'];
    if (isset($_SESSION['username'])) {
        $username = $_SESSION['username'];
    } else {
        if (isset($_POST['username'])) {
            if (failsStandardMobileChecksAndEchoFailureMessage()) {
                return;
            }
            $_SESSION['username'] = $username;
        }
    }

    if (!userExists($username)) {
        echo json_encode(array("error"=> "Current user '" . $username . "' does not exist"));
        return;
    }

    if (isBanned($username)) {
        echo json_encode(array("error"=>"User is banned"));
        return;
    }

    $sql = getSQL($username);
    if ($sql == -1) {
        echo json_encode(array("error"=>"Failed to prepare query"));
        return;
    }

    $lockSQL = "LOCK TABLES `mentionstbl` WRITE;";
    if (!mysql_query($lockSQL)) {
        echo json_encode(array("error"=>mysql_death1($lockSQL)));
        return;
    }


    $result = mysql_query($sql);

    if (!$result) {
        echo json_encode(array("error"=>mysql_death1($sql)));
        return;
    }

    $clearSQL = "UPDATE `mentionstbl` SET `hasBeenViewed` = 1 WHERE `fkMentionedName` = '" . $username . "';";
    if (!mysql_query($clearSQL)) {
        echo json_encode(array("error"=> mysql_death1($clearSQL)));
        return;
    }
    $unlockSQL = "UNLOCK TABLES;";
    if (!mysql_query($unlockSQL)) {
        echo json_encode(array("error"=> mysql_death1($unlockSQL)));
        return;
    }

    $booths = array();
    while ($row = mysql_fetch_array($result)) {
        $boothNum = $row['fkBoothNumber'];
        $mentioner = $row['fkMentionerName'];

        /**
         * @var $boothInfo MentionBoothInfo
         */
        $boothInfo = new MentionBoothInfo($boothNum, $row['fkIndex'], $link);
        $root = "http://" . $_SERVER['SERVER_NAME'] . "/";
        $booths[] = array(
            'boothnum' => $boothNum,
            'boothername' => strtolower((string)$boothInfo->boother),
            'bootherdisplayname' => (string)$boothInfo->boother,
            'mentioner' => $mentioner,
            'comment' => (string)$boothInfo->text,
            'image' => $root .$boothInfo->imageUrl,
            'smallImage' => $root .$boothInfo->smallImageUrl,
            'iconImage' => $root .$boothInfo->iconImageUrl);
    }



    echo json_encode($booths);

}

function getSQL($username)
{
    $pageNum = 1;
    if (isset($_POST['pagenum'])) {
        $pageNum = $_POST['pagenum'];
    }

    return getMentionsSQL($username, $pageNum, 10);
}