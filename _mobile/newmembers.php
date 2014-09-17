<?php

session_start();
error_reporting(0);
main();

function main() {

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

    $sql =
        "SELECT `boothnumbers`.`pkNumber` as `pkNumber`,
            `boothnumbers`.`imageTitle` as `imageTitle`,
            `boothnumbers`.`filetype` as `filetype`,
            `boothnumbers`.`blurb` as `blurb`,
            `logintbl`.`username` as `fkUsername`
        FROM `boothnumbers`
        INNER JOIN `logintbl`
        ON `boothnumbers`.`fkUsername`=`logintbl`.`username`
        WHERE `fkUsername`
        IN ( SELECT `fkUsername` FROM `userspublictbl` )
        AND `isPublic` = 1
        GROUP BY `fkUsername`
        ORDER BY `joinDate` DESC LIMIT " . $numperpage * ($pagenum - 1) . ", ".$numperpage.";";
    $result = mysql_query($sql);

    if (!$result) {
        echo mysql_death1($sql);
        return;
    }

    $booths = array();
    while($row = mysql_fetch_array($result)) {
        $booths[] = array(
            'boothnum' => $row['pkNumber'],
            'boothername' => $row['fkUsername'],
            'bootherdisplayname' => (string)getDisplayName($row['fkUsername']),
            'blurb' => $row['blurb'],
            'imageHash' => $row['imageTitle'],
            'filetype' => $row['filetype']);
    }
    echo json_encode($booths);

}
