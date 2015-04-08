<?php

    if (!isset($_SESSION)) session_start();
    error_reporting(0);
    main();

    function main() {

        require_once("{$_SERVER['DOCUMENT_ROOT']}/livefeed/utils.php");
        require_once("{$_SERVER['DOCUMENT_ROOT']}/_mobile/utils.php");
        require_once("{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php");
        require_common("db");
        require_common("utils");

        $dblink = connect_boothDB();

        $sql = getSQL();
        $result = $dblink->query($sql);

        if (!$result) {
            echo json_encode(
                array(
                    "error" => sql_death1($sql)
                )
            );
            return;
        }

        $booths = array();
        $newestBooth = -1;
        while($row = $result->fetch_array()) {
            $root = base();
            $booths[] = array(
                'boothnum' => $row['pkNumber'],
                'boothername' => $row['fkUsername'],
                'bootherdisplayname' => (string)getDisplayName($row['fkUsername']),
                'blurb' => $row['blurb'],
                'imageHash' => $row['imageTitle'],
                'filetype' => $row['filetype'],
                'absoluteImageUrlThumbnail' => $root . '/booths/small/' . $row['imageTitle'] . '.' . $row['filetype'],
                'absoluteImageUrl' => $root . '/booths/' . $row['imageTitle'] . '.' . $row['filetype']);
            $newestBooth = $row['pkNumber'];
        }
        echo json_encode(
            array(
                "success" => array(
                    "booths" => $booths,
                    "next_batch_start_booth_number" => $newestBooth
                )
            )
        );

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

    $newerThanBoothNumber = -1;
    if (isset($_POST['newer_than_booth_number'])) {
        $newerThanBoothNumber = $_POST['newer_than_booth_number'];
    }

    if (isset($_POST['username'], $_POST['phoneid'], $_POST['loginkey'])) {
        $username = $_POST['username'];
        $_SESSION['username'] = $username;
        $check = isKeyOK($username, $_POST['phoneid'], $_POST['loginkey']);
        if ($check == OK) {
            return getMyPublicFeedSQL($username, $pageNum, $numPerPage, $newerThanBoothNumber);

        }
    }
    return getPublicFeedSQL($pageNum, $newerThanBoothNumber);
}

function getMyPublicFeedSQL($username, $pageNum, $numPerPage, $newerThanBoothNumber=-1) {
    if (isset($_POST['includeFriends'])) {
        if ($_POST['includeFriends'] == false || $_POST['includeFriends'] == "false") {
           return getNonFriendPublicFeedSQL($username, $pageNum, $numPerPage, $newerThanBoothNumber);
        }
    }
    return getUserPublicFeedSQL($username, $pageNum, $numPerPage, 0, $newerThanBoothNumber);
}
