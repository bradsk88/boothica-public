<?php

session_start();
error_reporting(0);
main();

function main() {

    require_once("{$_SERVER['DOCUMENT_ROOT']}/userpages/friendlist_utils.php");
    require_once("{$_SERVER['DOCUMENT_ROOT']}/_mobile/utils.php");
    require_once("{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php");
    require_common("db");
    require_common("utils");

    $link = connect_to_boothsite();
    update_online_presence();

    $username = $_POST['username'];
    if (isset($_SESSION['username'])) {
        $username = $_SESSION['username'];
    }
    else if (failsStandardMobileChecksAndEchoFailureMessage()) {
        return;
    }

    $pagenum = 1;
    if (isset($_POST['pagenum'])) {
        $pagenum = $_POST['pagenum'];
    }

    $numperpage = 10;
    if (isset($_POST['numperpage'])) {
        $numperpage = $_POST['numperpage'];
    }

    $sql = "SELECT count(*) AS `num` FROM `friendstbl` WHERE `fkUsername` = '".$username."'";
    $query = sql_query($sql);
    $num = sql_get_expectOneRow($query, "num");
    if ($num == 0) {
        echo json_encode(array());
        return;
    }

    $sql = getRecentFriendshipsAmongstFriends($username, $pagenum, $numperpage);
    $result = mysql_query($sql);

    if (!$result) {
        echo mysql_death1($sql);
        return;
    }

    $booths = array();
    while($row = mysql_fetch_array($result)) {
        $booths[] = array(
            'follower' => $row['follower'],
            'followerImg' => (string) UserImage::getImage($row['follower']),
            'followee' => $row['followee'],
            'followeeImg' => (string) UserImage::getImage($row['followee'])
        );
    }
    echo json_encode($booths);

}
