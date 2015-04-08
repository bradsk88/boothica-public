<?php

session_start();
error_reporting(0);
main();

function main()
{

    require_once("{$_SERVER['DOCUMENT_ROOT']}/common/user_utils.php");
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
    else if (isset($_POST['username']) && failsStandardMobileChecksAndEchoFailureMessage()) {
        return;
    }
    $_SESSION['username'] = $username;


    if (!userExists($username)) {
        echo json_encode(
            array(
                "error"=>"Current user '". $username ."' does not exist"));
        return;
    }

    if (parameterIsMissingAndEchoFailureMessage("boothername")) {
        return;
    }

    $bootherName = strtolower($_POST['boothername']);
    if (!userExists($bootherName)) {
        echo json_encode(
            array(
                "error"=>"Boother '". $bootherName ."' does not exist"));
        return;
    }

    if (isBanned($username)) {
        echo json_encode(
            array(
                "error"=>"User is banned"));
        return;
    }

    if (!isPublic($bootherName)) {
        if (!isFriendOf($username, $bootherName)) {
            echo json_encode(
                array(
                    "error"=>"Private user"));
            return;
        }
    }

    $sql = getSQL();
    if ($sql == -1) {
        return;
    }

    $result = mysql_query($sql);

    if (!$result) {
        echo json_encode(
            array(
                "error"=>mysql_death1($sql)));
        return;
    }

    $booths = array();
    while ($row = mysql_fetch_array($result)) {
        $root = "http://" . $_SERVER['SERVER_NAME'];
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
    echo json_encode($booths);

}

function getSQL()
{
    $pageNum = 1;
    if (isset($_POST['pagenum'])) {
        $pageNum = $_POST['pagenum'];
    }

    $numPerPage = 10;
    if (isset($_POST['numperpage'])) {
        $numPerPage = $_POST['numperpage'];
    }

    $newerThan = -1;
    if (isset($_POST['newer_than_booth_number'])) {
        $newerThan = $_POST['newer_than_booth_number'];
    }

    return getBoothsSQL(strtolower($_POST['boothername']), $pageNum, $numPerPage, $newerThan);
}