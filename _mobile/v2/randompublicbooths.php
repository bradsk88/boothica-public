<?php

error_reporting(E_ALL);
session_start();
main();

function main() {

    require_once("{$_SERVER['DOCUMENT_ROOT']}/livefeed/utils.php");
    require_once("{$_SERVER['DOCUMENT_ROOT']}/_mobile/utils.php");
    require_once("{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php");
    require_common("db");
    require_common("utils");

    $numPerPage = 9;
    if (isset($_POST['numperpage'])) {
        $numPerPage = $_POST['numperpage'];
    }

    $sql = "SELECT MAX(`pkNumber`) as 'max' FROM `boothnumbers`";
    $result = sql_query($sql);

    if (!$result) {
        echo json_encode(
            array(
                "error"=>sql_death1($sql)
            )
        );
        return;
    }

    $max = sql_get_expectOneRow($result, "max");
    $sql = "SELECT * FROM `boothnumbers` bn WHERE (";

    for ($i = 0; $i < $numPerPage+10; $i++) {
        $sql .= "`pkNumber` = '".rand(1,$max)."' OR ";
    }

    $sql = substr($sql, 0, strlen($sql)-4)."
    )
    AND
			(
			bn.`isPublic` = true
			AND
			(SELECT `password` FROM `logintbl` WHERE `username` = bn.`fkUsername`)
			IN (
				SELECT `fkPassword`
				FROM `userspublictbl`
				WHERE `fkUsername` = bn.`fkUsername`
			))
    LIMIT ".$numPerPage.";";

    if ($_POST['debug']) {
        echo json_encode(array(
            "debug"=>"Email of SQL sent to devlist",
            "error"=>"Special command recieved. See 'debug'"
        ));
        death($sql);
        return;
    }

    if (rand(0,100) == 0 || $_POST['force_integrity_check']) {
        try {
            doRandomIntegrityCheck($sql);
        } catch (Exception $e) {
            death($e->getMessage());
            //do nothing
        }
    }

    unset($result);
    $result = mysql_query($sql);

    if (!$result) {
        echo json_encode(
            array(
                "error"=>mysql_death1($sql)
            )
        );
        return;
    }

    $booths = array();
    $root = "http://" . $_SERVER['SERVER_NAME'];
    while($row = mysql_fetch_array($result)) {
        $booths[] = array(
            'boothnum' => $row['pkNumber'],
            'boothername' => $row['fkUsername'],
            'bootherdisplayname' => (string)getDisplayName($row['fkUsername']),
            'blurb' => $row['blurb'],
            'imageHash' => $row['imageTitle'],
            'filetype' => $row['filetype'],
            'absoluteImageUrlThumbnail' => $root . '/booths/small/' . $row['imageTitle'] . '.' . $row['filetype']
        );
    }
    echo json_encode(array("success" =>
        array("booths" => $booths)
    ));

}

function doRandomIntegrityCheck($sql) {
    $r = mysql_query($sql);

    if (!$r) {
        echo json_encode(array("error"=>mysql_death1($sql)));
        return;
    }

    while($row = mysql_fetch_array($r)) {
        if (isBoothPublic($row['pkNumber'])) {
            continue;
        }
        death ("Random public booths included private booth number: ".$row['pkNumber']);
    }
}