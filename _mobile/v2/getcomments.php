<?php
/**
 * Created by PhpStorm.
 * User: Brad
 * Date: 3/2/14
 * Time: 8:42 PM
 */
use comment\Comments;
use comment\CommentObj;

require_once("{$_SERVER['DOCUMENT_ROOT']}/_mobile/utils.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/_mobile/v2/meta/AbstractUserApiResponse.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/comment/CommentObj.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/comment/Comments.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/comment/comment_utils.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/booth/utils.php");
require_common("utils");
require_common("internal_utils");
require_asset("UserImage");

class GetCommentsApiResponse extends AbstractUserApiResponse {

    function __construct() {
        parent::__construct(array("boothnum"));
    }

    /**
     * This should be implemented by descendants but should not be called directly.  Use runAndEcho.
     */
    protected function run($username)
    {
        $boothnumber = $_POST['boothnum'];

        if (isAllowedToInteractWithBooth($_SESSION['username'], $boothnumber)) {
            $getComments = $this->doGetComments($boothnumber);
            if (isset($getComments['success'])) {
                $this->markCallAsSuccessful("Comments get OK", array("comments" => $getComments['success']));
            } else {
                $this->markCallAsFailure($getComments['error']);
            }
            return;
        }

        $this->markCallAsFailure($username." is not allowed to view comments on booth ".$boothnumber);
        return;
    }

    function doGetComments($boothnumber) {
        $comments = Comments::loadForBooth($boothnumber);

        if (isset($comments['success'])) {
            return array(
                "success" => $this->toArray($comments["success"])
            );
        } else {
            return $comments;
        }
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


            $root = base();
            $canDeleteComment = isAllowedToDeleteCommentNumber($_SESSION['username'], $comment->getCommentNumber());
            $commenterName = $comment->getCommenterName();
            $iconImage = UserImage::getImage($commenterName);
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
                    'absoluteIconImageUrl' => $root.$iconImage,
                    'mediaType' => 'photo'
                );
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
                    'absoluteIconImageUrl' => $root.$iconImage,
                    'mediaType' => 'none'
                );

            }
        }
        return $out;
    }
}

if (!isset($_SESSION)) session_start();
error_reporting(0);

$page = new GetCommentsApiResponse();
$page->runAndEcho();
