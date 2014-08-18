<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Brad
 * Date: 9/10/13
 * Time: 12:28 AM
 * To change this template use File | Settings | File Templates.
 */

require_once "{$_SERVER['DOCUMENT_ROOT']}/common/assets/AbstractCommentCell.php";

use comment\CommentObj;
require_once("{$_SERVER['DOCUMENT_ROOT']}/comment/CommentObj.php");

class PhotoCommentCell extends AbstractCommentCell {

    CONST FRAME = "commentpicframe";
    CONST TEXT = "commentpictext";
    CONST PIC_HEIGHT = 200;

    private $height;


    /**
     * @var $comment CommentObj
     */
    function __construct($comment, $boothername) {
        parent::__construct(TextCommentCell::FRAME, TextCommentCell::TEXT, $comment, $boothername);
        $this->height = $height = TextCommentCell::PIC_HEIGHT * $comment->getImageHeightWidthProp();
    }

    public function image()
    {
        return "
							<div class = 'commentpic' style='height: " . $this->height . "px; background-image: url(/comments/" . $this->hash . ".jpg);'>
							</div>";
    }

    public function postedAComment()
    {
        return "posted a photo comment.";
    }

}