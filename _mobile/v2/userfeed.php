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

    $dblink = connect_boothDB();

    $username = $_POST['username'];
    if (isset($_SESSION['username'])) {
        $username = $_SESSION['username'];
    } else if (isset($_POST['username']) && failsStandardMobileChecksAndEchoFailureMessage()) {
        return;
    }
    $_SESSION['username'] = $username;

    if (parameterIsMissingAndEchoFailureMessage("boothername")) {
        return;
    }

    $bootherName = strtolower($_POST['boothername']);
    if (!userExists($bootherName)) {
        echo json_encode(
            array(
                "error" => "Boother '" . $bootherName . "' does not exist"));
        return;
    }

    if (isBanned($username)) {
        echo json_encode(
            array(
                "error" => "User is banned"));
        return;
    }

    if (!isPublic($bootherName)) {
        if (!isFriendOf($username, $bootherName)) {
            echo json_encode(
                array(
                    "error" => "Private user"));
            return;
        }
    }

    $sql = getSQL();
    if ($sql == -1) {
        return;
    }

    $dblink = connect_boothDB();
    $result = $dblink->query($sql);

    if (!$result) {
        echo json_encode(
            array(
                "error" => sql_death1($sql)));
        return;
    }

    $booths = array();
    while ($row = $result->fetch_array()) {
        $root = base();
        $booths[] = array(
            'boothnum' => $row['pkNumber'],
            'boothername' => $row['fkUsername'],
            'bootherdisplayname' => (string)getDisplayName($row['fkUsername']),
            'blurb' => $row['blurb'],
            'imageHash' => $row['imageTitle'],
            'filetype' => $row['filetype'],
            'absoluteImageUrlThumbnail' => $root . '/booths/small/' . $row['imageTitle'] . '.' . $row['filetype']
        );
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
        if ($_POST['pagenum'] < 1) {
            echo json_encode(array('error' => "pagenum ".$_POST['pagenum']." is invalid.  Page numbers start at 1."));
            return -1;
        }
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
