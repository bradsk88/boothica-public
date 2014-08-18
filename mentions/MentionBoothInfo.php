<?php
/**
 * Created by PhpStorm.
 * User: Brad
 * Date: 5/17/14
 * Time: 10:33 PM
 */

class MentionBoothInfo {

    public $boother;
    public $text;
    public $imageUrl;

    public function __construct($boothNumber, $index, $dblink) {
        $boothsql = "SELECT `fkUsername`, `imageTitle`, `filetype` FROM `boothnumbers` WHERE `pkNumber` = ".$boothNumber." LIMIT 1;";
        $boothresult = mysql_query($boothsql);
        if (!$boothresult) {
            mysql_death1($boothsql);
            debug("Boothresult was null");
            return;
        }
        if (mysql_num_rows($boothresult) == 0) {
            debug("Boothresult was empty");
            echo "
                    <div class = \"mentionframe\" style = \"height: 20px !important;\">
                    Booth Deleted
                    </div>";
            return;
        }
        $bootherrow = mysql_fetch_array($boothresult);
        $this->boother = new DisplayName($bootherrow['fkUsername']);
        debug("Boother for this mention is ".$this->boother);
        if ($index == -1) {
            $textsql = "SELECT `blurb` FROM `boothnumbers` WHERE `pkNumber` = ".$boothNumber." LIMIT 1";
            $textres = mysql_query($textsql);
            if (!$textres) {
                mysql_death1($textsql);
                return;
            }
            $textrow = mysql_fetch_array($textres);
            $this->text = stripslashes(trim($textrow['blurb'], "'"));
        } else {
            $commentsql = "SELECT `commentBody` FROM `commentstbl` WHERE `pkCommentNumber` = ".$index." LIMIT 1;";
            $commentrow = mysql_fetch_array(mysql_query($commentsql));
            $this->text = stripslashes(trim($commentrow['commentBody'], "'"));
        }
        $file = $bootherrow['imageTitle'] . "." . $bootherrow['filetype'];
        $this->imageUrl = "/booths/".$file;
        $this->smallImageUrl = "booths/small/".$file;
        $this->iconImageUrl = "booths/tiny/".$file;
    }

    public function getImageurl() {
        return $this->imageUrl;
    }

}