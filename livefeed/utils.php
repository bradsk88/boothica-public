<?php
/**
 * Created by PhpStorm.
 * User: Brad
 * Date: 4/30/14
 * Time: 11:52 PM
 */

/**
 * @param $username
 * @param $pageNum
 * @param $numperpage
 * @param $numToSkip
 * @return string
 */
function getUserPublicFeedSQL($username, $pageNum, $numperpage, $numToSkip, $newerThanBoothNumber=-1)
{
    $additionalCheck = "";
    $additionalBracket = "";
    if ($newerThanBoothNumber > 0) {
        $additionalCheck = ") AND pkNumber > ".$newerThanBoothNumber;
        $additionalBracket = "(";
    }
    $start = ($numperpage * ($pageNum - 1)) + $numToSkip;
    $sql = "
		SELECT *
		FROM `boothnumbers` bn
		WHERE " . $additionalBracket . "
			(
			bn.`isPublic` = true
			AND
			(SELECT `password` FROM `logintbl` WHERE `username` = bn.`fkUsername`)
			IN (
				SELECT `fkPassword`
				FROM `userspublictbl`
				WHERE `fkUsername` = bn.`fkUsername`
			))
			OR
			(
			bn.`fkUsername`
			IN (
				SELECT `fkUsername`
				FROM `friendstbl`
				WHERE `fkFriendName` = '" . $username . "')
			)
        " . $additionalCheck . "
		ORDER BY bn.`datetime` DESC
		LIMIT " . $start . ", " . $numperpage . ";";
    return $sql;
}


function getNonFriendPublicFeedSQL($username, $pageNum, $numOfPages, $newerThanBoothNumber=-1)
{
    $additionalCheck = "";
    if ($newerThanBoothNumber > 0) {
        $additionalCheck = "AND pkNumber > ".$newerThanBoothNumber;
    }
    return "
		SELECT *
		FROM `boothnumbers` bn
		WHERE
			(
			bn.`isPublic` = true
			AND
			(SELECT `password` FROM `logintbl` WHERE `username` = bn.`fkUsername`)
			IN (
				SELECT `fkPassword`
				FROM `userspublictbl`
				WHERE `fkUsername` = bn.`fkUsername`
			))
	        AND
            (
			bn.`fkUsername`
			NOT IN (
				SELECT `fkFriendname`
				FROM `friendstbl`
				WHERE `fkUsername` = '" . $username . "')
			)
		ORDER BY bn.`datetime` DESC
		".$additionalCheck."
		LIMIT " . $numOfPages * ($pageNum - 1) . ", ".$numOfPages.";";
}
/**
 * @param $pageNum
 * @return string
 */
function getPublicFeedSQL($pageNum, $numPerPage = 9, $newerThanBoothNumber=-1)
{
    $additionalCheck = "";
    if ($newerThanBoothNumber > 0) {
        $additionalCheck = "AND pkNumber > ".$newerThanBoothNumber;
    }
    return "
		SELECT *
		FROM `boothnumbers` bn
		WHERE
		    bn.`isPublic` = true
			AND
			(SELECT `password` FROM `logintbl` WHERE `username` = bn.`fkUsername`)
			IN (
				SELECT `fkPassword`
				FROM `userspublictbl`
				WHERE `fkUsername` = bn.`fkUsername`
			)
        ".$additionalCheck."
		ORDER BY bn.`datetime` DESC
		LIMIT " . $numPerPage * ($pageNum - 1) . ", ".$numPerPage.";";
}