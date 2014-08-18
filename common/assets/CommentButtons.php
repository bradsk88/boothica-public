<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Brad
 * Date: 9/10/13
 * Time: 12:38 AM
 * To change this template use File | Settings | File Templates.
 */

final class ShowDelete {
    const NO = 0;
    const STD = 1;
    const MOD = 2;
}

session_start();

/**
 * Class CommentButtons
 * Requires database $link to be open.
 * Requires open session with username.
 */
class CommentButtons {

    private $showDelete;
    private $commentnumber;
    private $commentindex;
    private $commentername;
    private $isModerator;
    private $curUserIsMod;
    private $ownComment;
    private $boothername;
    private $boothnumber;
    private $hasLiked;
    private $hasDisliked = false;

    function __toString() {
        $str = "<div class = \"commentbuttons\">";
        $str .= $this->deleteButton();
        $str .= $this->userType();
        $str .= $this->badPersonButtons();
        $str .= $this->likeButtons();
        $str .= "</div>";
        return $str;
    }

    function likeButtons() {
        //TODO: bring back dislike
        if ($this->hasLiked) {
            //echo " <a href = 'javascript:dislike_comment(".$commentindex . ")'><img src= \"/media/thumbzdn.png\" title = \"I don't like this\"></a>";
            return "
		<img src= \"/media/thumbzup_y.png\" title = \"You like this!\">";
        }
        if ($this->hasDisliked) {
            //echo " <img src= \"/media/thumbzdn_y.png\" title = \"You don't like this\">";
            return "
		<a href = 'javascript:like_comment(".$this->commentindex.")'>
			<img src= \"/media/thumbzup.png\" title = \"I like this!\">
		</a>";
        }

        //echo " <a href = 'javascript:dislike_comment(".$commentindex . ")'><img src= \"/media/thumbzdn.png\" title = \"I don't like this\"></a>";
            return "
		<a href = 'javascript:like_comment(".$this->commentindex.")'>
			<img src= \"/media/thumbzup.png\" title = \"I like this!\" onclick=\"this.src='/media/thumbzup_y.png'\">
		</a>";
    }

    function badPersonButtons() {
        $r = $this->doBadPersonButtons();
        if ($this->ownComment) {
            return $r;
        }
        $r .= "
		<a href = 'javascript:ignore_user(\"".$this->commentername."\",\"".$this->boothername."\",\"".$this->boothnumber."\")'>
			<img src= \"/media/ignore.png\" title = \"Ignore this user\">
		</a>
		&nbsp;&nbsp;&nbsp;&nbsp;
		";
        return $r;
    }

    function doBadPersonButtons() {
        if ($this->curUserIsMod) {
            return $this->banButtons();
        } else {
            return $this->reportButtons();
        }
    }

    function reportButtons() {
        return "
		<a href = 'javascript:open_report_user(\"".$this->commentername."\",\"".$this->commentindex."\")'>
			<img src= \"/media/report.png\" title = \"Report User\">
		</a>";
    }

    function banButtons() {
        return "
		<a href = 'javascript:open_suspend_user(\"".$this->commentername."\",\"".$this->commentnumber."\")'>
			<img src= \"/media/suspend.png\" title = \"Suspend user's account\">
		</a>";
    }

    function userType() {
        if ($this->isModerator) {
            return "<img src = \"/media/mod.png\" title = \"Moderator\">";
        } else if (isBanned($this->commentername)) {
            return "<font color = '#CC0000'>Banned</font>";
        } else if (isSuspended($this->commentername)) {
            return "<font color = '#0000CC' title = 'User was supended for violation of rules'>Suspended</font>";
        }
        return "";
    }

    function __construct($commentername, $commentnumber, $commentindex, $boothername) {
        $this->commentnumber = $commentnumber;
        $this->commentername = $commentername;
        $this->commentindex = $commentindex;
        $this->isModerator = isModerator($commentername);
        $username = $_SESSION['username'];
        $this->ownComment = ($username == $commentername);
        $this->boothnumber = $_GET['number'];
        $this->boothername = $boothername;
        $this->curUserIsMod = isModerator($username);
        $this->hasLiked = $this->hasLiked();

    }

    public function deleteButton()
    {
        if ($this->showDelete = ShowDelete::STD) {
            return "
		<a href = 'javascript:delete_comment(\"" . $this->commentnumber . "\")'>
			<img src= \"/media/delete.png\" title = \"delete\">
		</a>";
        }
        if ($this->showDelete = ShowDelete::MOD) {
            return "
                    <a href = 'javascript:modDeleteComment(\"".$this->commentnumber."\", \"".$this->commentername."\")'>
			<img src= \"/media/delete.png\" title = \"delete\">
		</a>";
        }
        return "";
    }

    function hasLiked() {

        $sql = "SELECT true FROM `likes_commentstbl`
			WHERE `fkCommentNumber` = ".$this->commentindex."
			AND `fkUsername` = '".$_SESSION['username']."'
			AND `value` = 1
			LIMIT 2;";
        $query = mysql_query($sql);
        if ($query) {
            if (mysql_num_rows($query) == 1) {
                return true;
            } else if (mysql_num_rows($query) != 0) {
                death("Multiple entries in comment likes table for ".$this->commentindex.",".$_SESSION['username'].".");
            }
        } else {
            mysql_death1($sql);
        }
        return false;

    }

    function hasDisliked($commentindex) {

        return false;

    }
}