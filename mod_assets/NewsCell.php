<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Brad
 * Date: 9/8/13
 * Time: 10:56 PM
 * To change this template use File | Settings | File Templates.
 */

require_once "{$_SERVER['DOCUMENT_ROOT']}/common/assets/Cell.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/common/assets/UserImage.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/common/assets/DisplayName.php";

class NewsCell extends Cell{

    private $string;

    public function __construct($posterName, $postNum, $date, $postBody, $postTitle ,$disableCommentLink) {
        parent::__construct($date);
        $userIcon = new UserImage($posterName);
        $displayName = new DisplayName($posterName);
        $this->string =  "
						<h2>" . $postTitle . "</h2>
						" . $postBody . "
						<br/>
						<br/>
                        <div style = \"width: 300px;\">
                            <a href = '/users/" . $posterName . "'>
								<div style = 'float: left; width: 18px; height: 18px; border-radius: 2px; background: url("
            . $userIcon . "); background-size: cover;'></div>
                            &nbsp;" . $displayName . "
                            </a>
                            <div style = 'clear: both;'></div>
                        </div>
		".parent::getDateString();
        if (!$disableCommentLink) {
            $this->string .= $this->numComments($postNum);
        }
    }

    function numComments($number) {

        $sql = "SELECT
			COUNT(*) as `num`
			FROM `news_commentstbl`
			WHERE `fkNumber` = ".$number.";";
        $commentsresult = mysql_query($sql);

        if (!$commentsresult) {
            $numrows = '?';
        } else {
            $rows = mysql_fetch_array($commentsresult);
            $numrows = $rows['num'];
        }
        return "
							<div class = 'feedcommand'>
								<a class = 'commentslink' href = '/news/".$number."'>"
            .$numrows." comments posted
								</a>
							</div>
	";
    }

    public function __toString() {
        return $this->string;
    }

}