<?php
/**
 * Created by PhpStorm.
 * User: Brad
 * Date: 5/6/14
 * Time: 11:10 PM
 */

function getMentionsSQL($username, $pageNum, $perPage) {
    $startnum = $perPage * ($pageNum-1);
    return "SELECT
						`fkMentionerName`,
						`fkBoothNumber`,
						`fkIndex`,
						`datetime`,
						`location`
						FROM `mentionstbl`
						WHERE lower(`fkMentionedName`) = '".$username."'
						ORDER BY `datetime` DESC
						LIMIT ".$startnum.", ".$perPage.";";
}