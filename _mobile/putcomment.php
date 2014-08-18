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
require_once("{$_SERVER['DOCUMENT_ROOT']}/_mobile/utils.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/comment/CommentObj.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/comment/Comments.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/actions/comment/utils.php");
require_common("utils");
require_common("internal_utils");

$link = connect_to_boothsite();
update_online_presence();
putComment();

function putComment() {
    if (isset($_POST['username'], $_POST['phoneid'], $_POST['loginkey'], $_POST['boothnum'], $_POST['commenttext'])) {
        $username = $_POST['username'];
        $check = isKeyOK($username, $_POST['phoneid'], $_POST['loginkey']);
        if ($check == OK) {
            $boother = $_POST['boother'];
            $_SESSION['username'] = $username;
            $res = upload_comment(false, $_POST['commenttext'], $_POST['boothnum'], $boother, ".jpg");
            unset($_SESSION['username']);
            echo $res;
            return;
        }
        echo -1;
    }
    print404Page();

}


function toArray($comments) {

    $out = array();

    /**
     * @var $comment CommentObj
     */
    foreach ($comments as $comment) {

        $out[] = array(
            'commentnum' => $comment->getCommentNumber(),
            'commentername' => $comment->getCommenterName(),
            'commenttext' => $comment->getCommentBody(),
            'imageHash' => $comment->getImageHash().$comment->getImageExtension());

    }

    return $out;

}
