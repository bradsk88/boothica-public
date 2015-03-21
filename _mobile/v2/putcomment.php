<?php
/**
 * Created by PhpStorm.
 * User: Brad
 * Date: 3/2/14
 * Time: 8:42 PM
 */
use comment\CommentObj;

error_reporting(0);

require_once("{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/booth/utils.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/_mobile/utils.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/comment/CommentObj.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/comment/Comments.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/actions/comment/utils.php");
require_common("utils");
require_common("internal_utils");

session_start();
$link = connect_to_boothsite();
update_online_presence();
putComment();

function putComment()
{

    if (!isset($_SESSION)) session_start();

    $username = isset($_POST['username']) ? $_POST['username'] : null;
    if (isset($_SESSION['username'])) {
        $username = $_SESSION['username'];
    } else if (isset($_POST['username']) && failsStandardMobileChecksAndEchoFailureMessage()) {
        return;
    }
    $_SESSION['username'] = $username;

    if (parameterIsMissingAndEchoFailureMessage("boothnum")) {
        return;
    }
    $boothNum = $_POST['boothnum'];

    if (!isset($_POST['commenttext'])) {
        echo json_encode(array("error" => "Missing POST parameter commenttext"));
        return;
    }

    $boother = getBoothOwner($boothNum);
    $res = upload_comment(false, $_POST['commenttext'], $boothNum, $boother, ".jpg");
    if ($res == 0) {
        echo json_encode(array("success" => array("message" => "The comment was posted successfully")));
        return;
    }
    echo json_encode(array("error" => "General error"));
}


function toArray($comments)
{

    $out = array();

    /**
     * @var $comment CommentObj
     */
    foreach ($comments as $comment) {

        $out[] = array(
            'commentnum' => $comment->getCommentNumber(),
            'commentername' => $comment->getCommenterName(),
            'commenttext' => $comment->getCommentBody(),
            'imageHash' => $comment->getImageHash() . $comment->getImageExtension());

    }

    return $out;

}
