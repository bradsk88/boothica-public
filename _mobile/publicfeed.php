<?php

    session_start();
    error_reporting(0);
    main();

    function main() {

        require_once("{$_SERVER['DOCUMENT_ROOT']}/livefeed/utils.php");
        require_once("{$_SERVER['DOCUMENT_ROOT']}/_mobile/utils.php");
        require_once("{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php");
        require_common("db");
        require_common("utils");

        $link = connect_to_boothsite();
        update_online_presence();

        $sql = getSQL();
        $result = mysql_query($sql);

        if (!$result) {
            echo mysql_death1($sql);
            return;
        }

        $booths = array();
        while($row = mysql_fetch_array($result)) {
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

    $numPerPage = 9;
    if (isset($_POST['numperpage'])) {
        $numPerPage = $_POST['numperpage'];
    }

    if (isset($_SESSION['username'])) {
        return getMyPublicFeedSQL($_SESSION['username'], $pageNum, $numPerPage);
    }

    if (isset($_POST['username'], $_POST['phoneid'], $_POST['loginkey'])) {
        $username = $_POST['username'];
        $_SESSION['username'] = $username;
        $check = isKeyOK($username, $_POST['phoneid'], $_POST['loginkey']);
        if ($check == OK) {
            //TODO: This
            return getMyPublicFeedSQL($username, $pageNum, $numPerPage);
        }
    }

    return getPublicFeedSQL($pageNum);
}

function getMyPublicFeedSQL($username, $pageNum, $numPerPage) {
    if (isset($_POST['includeFriends'])) {
        if ($_POST['includeFriends'] == false || $_POST['includeFriends'] = "false") {
           return getNonFriendPublicFeedSQL($username, $pageNum, $numPerPage);
        }
    }
    return getUserPublicFeedSQL($username, $pageNum, $numPerPage, 0);
}
