<?php
/**
 * Created by PhpStorm.
 * User: Brad
 * Date: 5/23/14
 * Time: 8:00 PM
 */

    session_start();
    error_reporting(0);
    main();

    function main() {

        require_once("{$_SERVER['DOCUMENT_ROOT']}/userpages/friendlist_utils.php");
        require_once("{$_SERVER['DOCUMENT_ROOT']}/_mobile/utils.php");
        require_once("{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php");
        require_common("db");
        require_common("utils");

        $link = connect_to_boothsite();
        update_online_presence();

        $username = $_POST['username'];
        if (isset($_SESSION['username'])) {
            $username = $_SESSION['username'];
        } else {
            if (isset($_POST['username'])) {
                if (failsStandardMobileChecksAndEchoFailureMessage()) {
                    return;
                }
                $_SESSION['username'] = $username;
            }
        }
        $pageNum = 1;
        if (isset($_POST['pagenum'])) {
            $pageNum = $_POST['pagenum'];
        }
        $numPerPage = 10;
        if (isset($_POST['numperpage'])) {
            $numPerPage = $_POST['numperpage'];
        }

        $sql = getFriendsListPageSQL($username, $pageNum, $numPerPage);

        $res = sql_query($sql);
        $root = "http://" . $_SERVER['SERVER_NAME'];
        $friends = array();
        while($row = $res->fetch_assoc()) {
            $friends[] = array(
                'username' => $row['username'],
                'displayname' => (string)getDisplayName($row['username']),
                'lastonline' => $row['lastonline'],
                'iconImage' => $root. new UserImage($row['username'])
            );
        }

        echo json_encode($friends);

    }