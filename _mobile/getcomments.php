<?php
/**
 * Created by PhpStorm.
 * User: Brad
 * Date: 3/2/14
 * Time: 8:42 PM
 */
use comment\Comments;
use comment\CommentObj;

session_start();
error_reporting(0);

require_once("{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/_mobile/utils.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/comment/CommentObj.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/comment/Comments.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/comment/comment_utils.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/booth/utils.php");
require_common("utils");
require_common("internal_utils");
$link = connect_to_boothsite();
update_online_presence();
getComments();

function getComments() {
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


    if (!isset($_POST['boothnum'])) {
        echo "Missing parameter: boothnum";
        return;
    } else {
        $boothnumber = $_POST['boothnum'];
    }

    if (isBoothPublic($boothnumber)) {
        doGetComments($boothnumber);
        return;
    }
    if (isFriendOf($username, getBoothOwner($boothnumber))) {
        doGetComments($boothnumber);
        return;
    }

    echo json_encode(array());
    return;

}

function doGetComments($boothnumber) {
    echo json_encode(toArray(Comments::loadForBooth($boothnumber)));
    return;
}


function toArray($comments) {

    $out = array();

    /**
     * @var $comment CommentObj
     */
    foreach ($comments as $comment) {

        $sql = "SELECT
			COUNT(*) as `num`
			FROM `likes_commentstbl`
			WHERE `fkCommentNumber` = ".$comment->getCommentNumber().";";
        $numLikes = sql_get_expectOneRow(sql_query($sql), "num");

        $canDeleteComment = false;
        if (isset($_SESSION['username'])) {
            $canDeleteComment = isAllowedToDeleteCommentNumber($_SESSION['username'], $comment->getCommentNumber());
        }
        if ($comment->hasPhoto()) {
            $hash = "/comments/".$comment->getImageHash().".".$comment->getImageExtension();
            $out[] = array(
                'commentnum' => $comment->getCommentNumber(),
                'commentername' => $comment->getCommenterName(),
                'commenterdisplayname' => (string)getDisplayName($comment->getCommenterName()),
                'commenttext' => $comment->getCommentBody(),
                'canDelete' => $canDeleteComment,
                'iconImage' => UserImage::getImage($comment->getCommenterName()),
                'imageHash' => $hash,
                'imageRatio' => $comment->getImageHeightWidthProp(),
                'likes' => $numLikes,
                'time' => $comment->getDateTimeStringReally());
        } else {
            $out[] = array(
                'commentnum' => $comment->getCommentNumber(),
                'commentername' => $comment->getCommenterName(),
                'commenterdisplayname' => (string)getDisplayName($comment->getCommenterName()),
                'commenttext' => $comment->getCommentBody(),
                'canDelete' => $canDeleteComment,
                'iconImage' => UserImage::getImage($comment->getCommenterName()),
                'likes' => $numLikes,
                'time' => $comment->getDateTimeStringReally());
        }


    }

    return $out;

}
