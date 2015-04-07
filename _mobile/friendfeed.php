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
    }
    else if (isset($_POST['username']) && failsStandardMobileChecksAndEchoFailureMessage()) {
        return;
    }
    $_SESSION['username'] = $username;

    if (!userExists($username)) {
        echo json_encode(
            array(
                "error"=>"Current user '".$username."' does not exist"));
        return;
    }

    if (isBanned($username)) {
        echo json_encode(
            array(
                "error"=>"User is banned"));
        return;
    }

    $sql = "SELECT COUNT(*) as count FROM friendstbl WHERE fkUsername = '".$username.";'";
    $countres = mysql_query($sql);
    if (!$countres) {
        sql_death1($sql);
    }
    $row = mysql_fetch_array($countres);
    if ($row['count'] == 0) {
        echo json_encode(array());
        return;
    }

    $sql = getSQL();
    if ($sql == -1) {
        return;
    }

    $result = $dblink->query($sql);

    if (!$result) {
        echo json_encode(
            array(
                "error"=>sql_death1($sql)));
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
    }
    echo json_encode($booths);

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

    if (isset($_SESSION['username'])) {
        return getFriendBoothsSQL($_SESSION['username'], $pageNum, $numberOfPages);
    }

    if (isset($_POST['username'], $_POST['phoneid'], $_POST['loginkey'])) {
        $username = $_POST['username'];
        $_SESSION['username'] = $username;
        $check = isKeyOK($username, $_POST['phoneid'], $_POST['loginkey']);
        if ($check == OK) {
            return getFriendBoothsSQL($username, $pageNum, $numberOfPages);
        }
        //TODO: More useful response
        echo "BadKey";
        return -1;
    }
    echo "MissingParam";
    return -1;
}