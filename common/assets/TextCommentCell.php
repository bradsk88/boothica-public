<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Brad
 * Date: 9/10/13
 * Time: 12:30 AM
 * To change this template use File | Settings | File Templates.
 */

require_once "{$_SERVER['DOCUMENT_ROOT']}/common/assets/AbstractCommentCell.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/common/assets/UserIcon.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/common/assets/CommentButtons.php";

use comment\CommentObj;
require_once("{$_SERVER['DOCUMENT_ROOT']}/comment/CommentObj.php");

class TextCommentCell extends AbstractCommentCell {

    private $icon;

    CONST FRAME = "commenticonframe";
    CONST TEXT = "commenttext";

    /**
     * @var $comment CommentObj
     */
    function __construct($comment, $boothername, $showCurrentIcon) {
        parent::__construct(TextCommentCell::FRAME, TextCommentCell::TEXT, $comment, $boothername);
        $this->icon = UserIcon::standardIcon($comment->getCommenterName());
    }

    /**
     * @var $comment CommentObj
     */
    public static function withCurrentIcon($comment, $boothername) {
        return new TextCommentCell($comment, $boothername, true);
    }

    /**
     * @var $comment CommentObj
     */
    public static function withPublicIcon($comment, $boothername) {
        return new TextCommentCell($comment, $boothername, false);
    }

    protected function image() {
        return "
			            <a href= '/users/" . $this->commentername . "/booths'>
			                ".$this->icon."
            </a>";
    }

    public function postedAComment()
    {
        return "posted a comment.";
    }

    public function __toString() {
       return parent::__toString();
    }
}