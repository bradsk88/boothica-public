<?php
/**
 * Created by PhpStorm.
 * User: Brad
 * Date: 5/25/14
 * Time: 12:58 PM
 */

session_start();
error_reporting(0);
main();

function main() {

    require_once("{$_SERVER['DOCUMENT_ROOT']}/_mobile/utils.php");
    require_once("{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php");
    require_once("{$_SERVER['DOCUMENT_ROOT']}/comment/comment_utils.php");
    require_common("db"); require_common("utils");

    $link = connect_to_boothsite();
    update_online_presence();

    $username = $_POST['username'];
    if (isset($_SESSION['username'])) {
        $username = $_SESSION['username'];
    }
    else if (failsStandardMobileChecksAndEchoFailureMessage()) {
        return;
    }
    $_SESSION['username'] = $username;

    if (parameterIsMissingAndEchoFailureMessage("commentnum")) {
        return;
    }
    $commentNum = $_POST['commentnum'];

    if (isAllowedToDeleteCommentNumber($username, $commentNum)) {

        $success = deleteCommentByNumber($commentNum, $link, $username);
        if ($success) {
            echo json_encode(array(
                "success" => $username." deleted comment number ".$commentNum."."
            ));
            return;
        }
        echo json_encode(
            array("error" => "Unexpected error.  Failed to delete comment."));
        return;
    }

    echo json_encode(
        array(
            "error" => "User ".$username." is not allowed to delete comment #".$commentNum."."));
    return;
}