<?php

    if (!isset($_SESSION)) session_start();
    error_reporting(0);
    main();

    function main() {

        require_once("{$_SERVER['DOCUMENT_ROOT']}/_mobile/utils.php");
        require_once("{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php");
        require_common("db");
        require_common("utils");
        require_lib("h2o-php/h2o");

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

    $newerThanBoothNumber = -1;
    if (isset($_POST['newer_than_booth_number'])) {
        $newerThanBoothNumber = $_POST['newer_than_booth_number'];
    }

    if (isLoggedIn()) {
        if (isset($_REQUEST['includeFriends']) && $_REQUEST['includeFriends'] == 'true') {
            $sqlBuilder = new h2o("{$_SERVER['DOCUMENT_ROOT']}/_mobile/v2/queries/publicfeed-loggedin.mst.sql");
        } else {
            $sqlBuilder = new h2o("{$_SERVER['DOCUMENT_ROOT']}/_mobile/v2/queries/publicfeed-loggedin-excludefriends.mst.sql");
        }
    } else {
        $sqlBuilder = new h2o("{$_SERVER['DOCUMENT_ROOT']}/_mobile/v2/queries/publicfeed-visitor.mst.sql");
    }

    $values = array(
        "startIndex" => max(($pageNum-1) * $numPerPage, 0),
        "numPerPage" => $numPerPage,
        "limitsGiven" => true,
        "username" => $_SESSION['username'] ? $_SESSION['username'] : null
    );
    if ($newerThanBoothNumber > 0) {
        $values['lowerBound'] = $newerThanBoothNumber;
    }
    return $sqlBuilder->render($values);

}
