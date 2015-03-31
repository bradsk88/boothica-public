<?php
/**
 * Created by PhpStorm.
 * User: Brad
 * Date: 5/21/14
 * Time: 9:51 PM
 */

//TODO: Lower this
error_reporting(E_ALL);
require_once("{$_SERVER['DOCUMENT_ROOT']}/userpages/friendbooth_utils.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/_mobile/utils.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php");
require_common("db");
require_common("utils");
require_once("{$_SERVER['DOCUMENT_ROOT']}/_mobile/v2/meta/AbstractUserApiResponse.php");

class FriendFeedActivity extends AbstractUserApiResponse {

    /**
     * This should be implemented by descendants but should not be called directly.  Use runAndEcho.
     */
    protected function run($username)
    {
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
                "next_batch_start_booth_number" => isset($newestBooth) ? $newestBooth : null
            )
        );
    }
}

$page = new FriendFeedActivity();
$page->runAndEcho();

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