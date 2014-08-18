<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Brad
 * Date: 9/10/13
 * Time: 12:27 AM
 * To change this template use File | Settings | File Templates.
 */

require_once "{$_SERVER['DOCUMENT_ROOT']}/common/assets/CommentDateStamp.php";

use comment\CommentObj;
require_once("{$_SERVER['DOCUMENT_ROOT']}/comment/CommentObj.php");

abstract class AbstractCommentCell {

    private $frame;
    private $text;
    private $commentbody;
    private $commentnumber;
    private $boothername;
    protected $commentername;
    protected $hash;

    /**
     * @var $comment CommentObj
     */
    function __construct($frame, $text, $comment, $boothername) {
        $this->frame = $frame;
        $this->text = $text;
        $this->commentbody = trim($comment->getCommentBody(), "'");
        $this->commentnumber = $comment->getCommentNumber();
        $this->commentername = $comment->getCommenterName();
        $this->hash = $comment->getImageHash();
        $this->boothername = $boothername;
        $this->dateStamp = new CommentDateStamp($comment);
    }

    function __toString() {
        $str =
            "
            <div class='".$this->frame."' id = '".$this->commentnumber."'>
                    <div>";
        $str .= $this->image();
        $str .= "
                            <div class='briefheader'>
								<a href= '/users/".$this->commentername."/booths'>
									".getDisplayName($this->commentername)."
								</a> ";
        $str .= $this->postedAComment();
        $str .= $this->dateStamp;
        $str .= "
							</div>
						</div>
						<hr color = #EEEEEE />
						<div class = '".$this->text."'>
						    ".stripslashes($this->commentbody).
            "</div>
             <div class = 'commentfooter'>";
        $commentButtons = new CommentButtons($this->commentername, $this->hash, $this->commentnumber, $this->boothername);
        $str .= $commentButtons;
        $str .= "
						</div>
					</div>
				";
        return $str;
        //todo: likes
//        printCommentLikes($commentnumber);
    }

    protected abstract function image();
    protected abstract function postedAComment();

}