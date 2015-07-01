<?php
/**
 * Created by PhpStorm.
 * User: Brad
 * Date: 5/21/14
 * Time: 9:51 PM
 */

error_reporting(0);
require_once("{$_SERVER['DOCUMENT_ROOT']}/_mobile/utils.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php");
require_common("db");
require_common("utils");
require_lib("h2o-php/h2o");
require_once("{$_SERVER['DOCUMENT_ROOT']}/_mobile/v2/meta/AbstractUserApiResponse.php");

class FriendFeedActivity extends AbstractUserApiResponse {

    /**
     * This should be implemented by descendants but should not be called directly.  Use runAndEcho.
     */
    protected function run($username)
    {
        $dblink = connect_boothDB();

        $sql = getSQL($dblink, $username);
        if ($sql == -1) {
            return;
        }

        $result = $dblink->query($sql);

        if (!$result) {
            $this->markCallAsFailure(sql_death1($sql));
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

        $data = array(
            "booths" => $booths,
            "next_batch_start_booth_number" => isset($newestBooth) ? $newestBooth : null
        );
        $this->markCallAsSuccessful("Booth get OK", $data);
    }
}

$page = new FriendFeedActivity();
$page->runAndEcho();

function getSQL($dblink, $username)
{
    //TODO: Add paging
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

    $template = "{$_SERVER['DOCUMENT_ROOT']}/_mobile/v2/queries/friendfeed-tofriend.mst.sql";
    if (isLoggedIn() && $_SESSION['username'] == $username) {
        $template = "{$_SERVER['DOCUMENT_ROOT']}/_mobile/v2/queries/friendfeed-owner.mst.sql";
    }
    $sqlBuilder = new h2o($template);


    $sql = $sqlBuilder->render(array(
        "username" => $dblink->real_escape_string($username),
        "current_username" => $dblink->real_escape_string($_SESSION['username'])
    ));
    return $sql;
}
