<?php
/**
 * Created by PhpStorm.
 * User: Brad
 * Date: 3/2/14
 * Time: 8:42 PM
 */
use comment\Comments;
use comment\CommentObj;

require_once("{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/_mobile/utils.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/comment/CommentObj.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/comment/Comments.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/comment/comment_utils.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/booth/utils.php");
require_common("utils");
require_common("internal_utils");
error_reporting(0);
$link = connect_to_boothsite();
update_online_presence();
getComments();

function getComments() {

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
    $boothnumber = $_POST['boothnum'];

    if (isBoothPublic($boothnumber)) {
        doGetComments($boothnumber);
        return;
    }
    if (isFriendOf($username, getBoothOwner($boothnumber))) {
        doGetComments($boothnumber);
        return;
    }

    echo json_encode(array("error" => $username." is not allowed to view comments on booth ".$boothnumber));
    return;

}

function doGetComments($boothnumber) {
    echo json_encode(array(
        "success" => toArray(Comments::loadForBooth($boothnumber))
    ));
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

        $root = "http://" . $_SERVER['SERVER_NAME'];
        if (isset($_SESSION['username'])) {
            $canDeleteComment = isAllowedToDeleteCommentNumber($_SESSION['username'], $comment->getCommentNumber());
        }
        $iconImage = UserImage::getImage($comment->getCommenterName());
        if ($comment->hasPhoto()) {
            $hash = "/comments/".$comment->getImageHash().".".$comment->getImageExtension();
            $out[] = array(
                'commentnum' => $comment->getCommentNumber(),
                'commentername' => $comment->getCommenterName(),
                'commenterdisplayname' => (string)getDisplayName($comment->getCommenterName()),
                'commenttext' => $comment->getCommentBody(),
                'canDelete' => $canDeleteComment,
                'iconImage' => $iconImage,
                'imageHash' => $hash,
                'imageRatio' => $comment->getImageHeightWidthProp(),
                'likes' => $numLikes,
                'time' => $comment->getDateTimeStringReally(),
                'absoluteImageUrl' => $root.$hash,
                'absoluteIconImageUrl' => $root.$iconImage);
        } else {
            $out[] = array(
                'commentnum' => $comment->getCommentNumber(),
                'commentername' => $comment->getCommenterName(),
                'commenterdisplayname' => (string)getDisplayName($comment->getCommenterName()),
                'commenttext' => $comment->getCommentBody(),
                'canDelete' => $canDeleteComment,
                'iconImage' => UserImage::getImage($comment->getCommenterName()),
                'likes' => $numLikes,
                'time' => $comment->getDateTimeStringReally(),
                'absoluteIconImageUrl' => $root.$iconImage);
        }


    }

    return $out;

}
