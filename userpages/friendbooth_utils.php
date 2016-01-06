<?php
/**
 * Created by PhpStorm.
 * User: Brad
 * Date: 5/22/14
 * Time: 10:43 PM
 */

function getFriendBoothsSQL($pageUser, $pageNum, $howMany)
{

    if ($_SESSION['username'] == $pageUser) {

        $sql = "SELECT b.`fkUsername`, b.`pkNumber`, b.`blurb`, b.`datetime`, b.`imageTitle`, b.`filetype`, b.`imageHeightProp`
                                FROM `boothnumbers` b
                                    WHERE b.`fkUsername` IN (
                                        SELECT `fkFriendname` FROM `friendstbl`
                                            WHERE lower(`fkUsername`) = '" . $pageUser . "')
                                            AND ('" . $pageUser . "' IN (
                                                SELECT `fkFriendname` FROM `friendstbl`
                                                    WHERE `fkUsername` = b.`fkUsername`)
                                            OR b.`fkUsername` IN (SELECT `fkUsername` FROM `usersprivacytbl` WHERE (privacyDescriptor = 'public' OR privacyDescriptor = 'semi-public'))
                                            )
                            ORDER BY b.`pkNumber` DESC LIMIT " . ($howMany * ($pageNum - 1)) . ", ".$howMany.";";
        return $sql;

    } else {

        //select the people who are this persons friend AND either my friend OR public
        $sql = "SELECT b.`fkUsername`, b.`pkNumber`, b.`blurb`, b.`datetime`, b.`imageTitle`, b.`filetype`, b.`imageHeightProp`
                                FROM `boothnumbers` b
                                    WHERE b.`fkUsername` IN (SELECT `fkFriendname` FROM `friendstbl` WHERE lower(`fkUsername`) = '" . $pageUser . "')
                                        AND '" . $pageUser . "' IN (SELECT `fkFriendname` FROM `friendstbl` WHERE `fkUsername` = b.`fkUsername`)
                                        AND ('" . $_SESSION['username'] . "' IN (SELECT `fkFriendname` FROM `friendstbl` WHERE `fkUsername` = b.`fkUsername`)
                                    OR (b.`fkUsername` IN (SELECT `fkUsername` FROM `userspublictbl`)
                                        AND (b.fkUsername IN (SELECT `fkUsername` FROM `usersprivacytbl` WHERE `fkUsername` = b.`fkUsername` AND (privacyDescriptor = 'public' OR privacyDescriptor = 'semi-public') LIMIT 1)))
                                        ORDER BY b.`pkNumber` DESC LIMIT " . ($howMany * ($pageNum - 1)) . ", ".$howMany.";";
        return $sql;

    }
}
