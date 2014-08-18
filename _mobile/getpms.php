<?php

session_start();
error_reporting(0);
main();

function main() {

    require_once("{$_SERVER['DOCUMENT_ROOT']}/_mobile/utils.php");
    require_once("{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php");
    require_common("db");
    require_common("utils");

    connect_to_boothsite();
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

    $sql = "SELECT `datetime`, `name` FROM (SELECT
			`dateTime`, `fromUsername` as `name`
			FROM `privatemsgtbl`
			WHERE `toUsername` =
			'".$username."'
			UNION
			SELECT
			`dateTime`, `toUsername` as `name`
			FROM `privatemsgtbl`
			WHERE `fromUsername` = '".$username."'
			ORDER BY `datetime` DESC) AS `X`
			GROUP BY `name`
			ORDER BY `datetime` DESC
			LIMIT " . $numperpage * ($pagenum - 1) . ", ".$numperpage.";";

    $result = mysql_query($sql);

    if (!$result) {
        echo mysql_death1($sql);
        return;
    }

    $booths = array();
    while($row = mysql_fetch_array($result)) {

        $fromusername = $row['name'];

        $sql = "SELECT COUNT(*) as `num`
					FROM `privatemsgtbl`
					WHERE `toUsername` = '".$username."'
					AND `fromUsername` = '".$fromusername."'
					AND `isRead` = '0'
					LIMIT 10";
        $result3 = mysql_query($sql);

        $num = 0;
        $hasnew = false;
        if ($result3) {
            $r = mysql_fetch_array($result3);
            $num = $r['num'];
            if ($num > 0) {
                $hasnew = true;
            }
            if ($num == 10) {
                $num = "9+";
            }
        }

        $booths[] = array(
            'username' => $fromusername,
            'userdisplayname' => (string)getDisplayName($fromusername),
            'iconImage' => (string) UserImage::getImage($fromusername),
            'hasnew' => $hasnew,
            'num' => $num
        );
    }
    echo json_encode($booths);

}