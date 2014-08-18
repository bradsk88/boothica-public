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
function getUserPublicFeedSQL($username, $pageNum, $numperpage, $numToSkip)
{
    $start = ($numperpage * ($pageNum - 1)) + $numToSkip;
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
			OR
			(
			bn.`fkUsername`
			IN (
				SELECT `fkUsername`
				FROM `friendstbl`
				WHERE `fkFriendName` = '" . $username . "')
			)
		ORDER BY bn.`datetime` DESC
		LIMIT " . $start . ", ".$numperpage.";";
}


function getNonFriendPublicFeedSQL($username, $pageNum, $numOfPages)
{
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
				SELECT `fkUsername`
				FROM `friendstbl`
				WHERE `fkFriendName` = '" . $username . "')
			)
		ORDER BY bn.`datetime` DESC
		LIMIT " . $numOfPages * ($pageNum - 1) . ", ".$numOfPages.";";
}
/**
 * @param $pageNum
 * @return string
 */
function getPublicFeedSQL($pageNum)
{
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
		ORDER BY bn.`datetime` DESC
		LIMIT " . 10 * ($pageNum - 1) . ", 10;";
}