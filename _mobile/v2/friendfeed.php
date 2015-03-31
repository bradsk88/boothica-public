<?php
/**
 * Created by PhpStorm.
 * User: Brad
 * Date: 5/21/14
 * Time: 9:51 PM
 */

error_reporting(0);
session_start();
main();

function main()
{

    require_once("{$_SERVER['DOCUMENT_ROOT']}/userpages/friendbooth_utils.php");
    require_once("{$_SERVER['DOCUMENT_ROOT']}/_mobile/utils.php");
    require_once("{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php");
    require_common("db");
    require_common("utils");

    $dblink = connect_boothDB();

    $username = $_POST['username'];
    if (isset($_SESSION['username'])) {
        $username = $_SESSION['username'];
    } else if (isset($_POST['username']) && failsStandardMobileChecksAndEchoFailureMessage()) {
        return;
    }
    $_SESSION['username'] = $username;

    if (!userExists($username)) {
        echo json_encode(
            array(
                "error" => "Current user '" . $username . "' does not exist"));
        return;
    }

    if (isBanned($username)) {
        echo json_encode(
            array(
                "error" => "User is banned"));
        return;
    }

    $sql = getSQL();
    if ($sql == -1) {
        return;
    }

    $result = sql_query($sql);

    if (!$result) {
        echo json_encode(
            array(
                "error" => sql_death1($sql)));
        return;
    }

    $booths = array();
    while ($row = $result->fetch_array()) {
        $root = "http://" . $_SERVER['SERVER_NAME'];
        $booths[] = array(
            'boothnum' => $row['pkNumber'],
            'boothername' => $row['fkUsername'],
            'bootherdisplayname' => (string)getDisplayName($row['fkUsername']),
            'blurb' => $row['blurb'],
            'imageHash' => $row['imageTitle'],
            'filetype' => $row['filetype'],
            'absoluteImageUrlThumbnail' => $root . '/booths/small/' . $row['imageTitle'] . '.' . $row['filetype']);
        $newestBooth = $row['pkNumber'];
    }
    echo json_encode(
        array("success" =>
            array(
                "booths" => $booths),
            "next_batch_start_booth_number" => $newestBooth
        )
    );
}

function getSQL()
{
    $pageNum = 1;
    if (isset($_POST['pagenum'])) {
        $pageNum = $_POST['pagenum'];
    }

    $numberOfPages = 10;
    if (isset($_POST['numberofbooths'])) { //backwards compatibility
        $numberOfPages = $_POST['numberofbooths'];
    }
    if (isset($_POST['numperpage'])) {
        $numberOfPages = $_POST['numperpage'];
    }

    return getFriendBoothsSQL($_SESSION['username'], $pageNum, $numberOfPages);
}