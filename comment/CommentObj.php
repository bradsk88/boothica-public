<?php
/**
 * Created by PhpStorm.
 * User: Brad
 * Date: 3/3/14
 * Time: 1:28 AM
 */

namespace comment;

class CommentObj {

    private $commentername;
    private $hasPhoto;
    private $commentBody;
    private $commentNumber;
    private $prop;
    private $hours;
    private $minutes;
    private $dateTime;
    private $hash;
    private $ext;

    public function __construct($commentername, $hasPhoto, $commentBody, $commentNumber, $prop, $hash, $ext, $datetime, $hours, $minutes) {
        checkNotNull_Msg($commentername, "Commenter name is null");
        checkNotNull_Msg($hasPhoto, "Has photo is null");
        if ($commentBody == null) {
            $commentBody = "";
        }
        $this->commentBody = $commentBody;
        checkNotNull_Msg($commentNumber, "Comment number is null");
        $this->hasPhoto = $hasPhoto;
        if ($this->hasPhoto()) {
            checkNotNull_Msg($prop, "Comment prop is null");
            checkNotNull_Msg($hash, "Comment hash is null");
            checkNotNull_Msg($ext, "Comment ext is null");
        }
        $this->commentername = $commentername;
        $this->commentBody = $commentBody;
        $this->commentNumber = $commentNumber;
        $this->prop = $prop;
        $this->hash = $hash;
        $this->ext = $ext;
        $this->hours = $hours;
        $this->minutes = $minutes;
        $this->dateTime = $datetime;
    }

    public function getCommenterName() {
        return $this->commentername;
    }

    public function hasPhoto() {
        return $this->hasPhoto;
    }

    public function getCommentBody() {
        return $this->commentBody;
    }

    public function getCommentNumber() {
        return $this->commentNumber;
    }

    public function getImageHeightWidthProp() {
        return $this->prop;
    }

    public function getDateTimeString() {
        if ($this->hours > 24) {
            echo "
				<div class = 'feeddate'>
					".	$this->dateTime."
				</div>";
        } else {
            $unit = "hours";
            if (1 == $this->hours) {
                $unit = "hour";
            }
            echo "
				<div class = 'feeddate'>
					".$this->hours." ".$unit.", " . $this->minutes. " minutes ago.
				</div>";
        }
    }

    public function getDateTimeStringReally() {
        if ($this->hours > 24) {
            return "".$this->dateTime."";
        } else {
            $unit = "hours";
            if (1 == $this->hours) {
                $unit = "hour";
            }
            return $this->hours." ".$unit.", " . $this->minutes. " minutes ago.";
        }
    }

    public static function fromSQL($row) {
        return new CommentObj($row['fkUsername'], $row['hasPhoto'], $row['commentBody'], $row['pkCommentNumber'], $row['imageHeightProp'], $row['hash'], $row['extension'], $row['datetime'], $row['hours'], $row['minutes']);
    }

    public function getImageHash()
    {
        return $this->hash;
    }

    public function getDateTime()
    {
        return $this->dateTime;
    }

    public function getHours()
    {
        return $this->hours;
    }

    public function getMinutes()
    {
        return $this->minutes;
    }

    public function getImageExtension()
    {
        return $this->ext;
    }

} 