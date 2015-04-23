<?php
/**
 * Created by PhpStorm.
 * User: Brad
 * Date: 3/2/14
 * Time: 8:42 PM
 */
use comment\CommentObj;

error_reporting(E_ALL);

require_once("{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/booth/utils.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/_mobile/v2/meta/AbstractUserApiResponse.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/_mobile/utils.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/comment/CommentObj.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/comment/Comments.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/actions/comment/utils.php");
require_common("utils");
require_common("internal_utils");

class PutCommentApiResponse extends AbstractUserApiResponse {

    /**
     * This should be implemented by descendants but should not be called directly.  Use runAndEcho.
     */
    protected function run($username)
    {
        if (parameterIsMissingAndEchoFailureMessage("boothnum")) {
            return;
        }
        $boothNum = $_POST['boothnum'];

        if (!isset($_POST['commenttext'])) {
            echo json_encode(array("error" => "Missing POST parameter commenttext"));
            return;
        }

        $commentText = $_POST['commenttext'];

        $boother = getBoothOwner($boothNum);

        $res = upload_comment(false, $commentText, $boothNum, $boother, "jpg");
        echo json_encode($res);
    }
}

if (!isset($_SESSION)) session_start();
$page = new PutCommentApiResponse();
$page->runAndEcho();
