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
require_once("{$_SERVER['DOCUMENT_ROOT']}/actions/comment/CommentFileUpload.php");
require_common("utils");
require_common("ImageUtils");
require_common("internal_utils");

class PutCommentApiResponse extends AbstractUserApiResponse {

    function __construct() {
        parent::__construct(array("boothnum", "commenttext", "mediatype", "image"));
    }

    /**
     * This should be implemented by descendants but should not be called directly.  Use runAndEcho.
     */
    protected function run($username)
    {

        if (!in_array($_POST['mediatype'], array("photo"))) {
            echo json_encode(array("error" => "Missing POST parameter mediatype.  Must be one of: [\"photo\",]"));
            return;
        }

        $commentText = $_POST['commenttext'];
        $image = $_POST['image'];
        $boothNum = $_POST['boothnum'];

        $boother = getBoothOwner($boothNum);

        $img = ImageUtils::makeFromEncoded($image);
        if (!$img) {
            death("decode failed");
            echo json_encode(array("error" => "Unexpected problem"));
            return;
        }
        $ext = ImageUtils::getExtensionOfEncoded($image);
        if ($ext == null) {
            echo json_encode(array(
                "error" => "Unable to determine filetype from: ".substr($image, 0, 10)."..."
            ));
            return;
        }
        $uploadok = CommentFileUpload::doFileUpload64($img, $ext, $commentText, $boothNum, $boother);
        if (isset($uploadok['success'])) {
            $uploadok['success']['boothUrl'] = base() . "/users/" . $boother . "/" . $boothNum;
        }
        echo json_encode($uploadok);
    }
}

if (!isset($_SESSION)) session_start();
$page = new PutCommentApiResponse();
$page->runAndEcho();
