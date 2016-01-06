<?php
/**
 * Created by PhpStorm.
 * User: Brad
 * Date: 5/19/14
 * Time: 9:22 PM
 */
require_once("{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php");
require_common("db");
require_common("utils");

function getBoothSQL($boothnumber) {
    return "SELECT
        `fkUsername`,
        `blurb`,
        `imageTitle`,
        `filetype`,
        `imageHeightProp`,
        `datetime`, `userBoothNumber`,
        HOUR( timediff( NOW( ) , `datetime` ) ) as `hours`,
        MINUTE( timediff( NOW( ) , `datetime` ) ) as `minutes`
        FROM `boothnumbers`
        WHERE `pkNumber`=".$boothnumber."
        LIMIT 1;";
}

function getPreviousBoothNumber($boothnumber, $boother) {
    $sql = "SELECT
        `pkNumber`
        FROM `boothnumbers`
        WHERE `pkNumber`<".$boothnumber."
   ";
    if (!canSeeBoothsFrom($boother)) {
        $sql .= "AND `isPublic` = true";
    }
    $sql .= "
        AND `fkUsername` = '".$boother."'
        ORDER BY `pkNumber` DESC
        LIMIT 1;";
    $query = sql_query($sql);
    return sql_get_expectOneRow($query, "pkNumber");
}

function getNextBoothNumber($boothnumber, $boother) {
    $sql = "SELECT
        `pkNumber`
        FROM `boothnumbers`
        WHERE `pkNumber`>".$boothnumber."
   ";
    if (!canSeeBoothsFrom($boother)) {
        $sql .= "AND `isPublic` = true";
    }
    $sql .= "
        AND `fkUsername` = '".$boother."'
        ORDER BY `pkNumber` ASC
        LIMIT 1;";
    $query = sql_query($sql);
    return sql_get_expectOneRow($query, "pkNumber");
}


function getLastBoothNumber($boother) {
    $sql = "SELECT
        `pkNumber`
        FROM `boothnumbers`
        WHERE `fkUsername` = '".$boother."'
   ";
    if (!canSeeBoothsFrom($boother)) {
        $sql .= "AND `isPublic` = true";
    }
    $sql .= "
        ORDER BY `pkNumber` DESC
        LIMIT 1;";
    $query = sql_query($sql);
    return sql_get_expectOneRow($query, "pkNumber");
}

function getFirstBoothNumber($boother) {
    $sql = "SELECT
        `pkNumber`
        FROM `boothnumbers`
        WHERE `fkUsername` = '".$boother."'
   ";
    if (!canSeeBoothsFrom($boother)) {
        $sql .= "AND `isPublic` = true";
    }
    $sql .= "
        ORDER BY `pkNumber` ASC
        LIMIT 1;";
    $query = sql_query($sql);
    return sql_get_expectOneRow($query, "pkNumber");
}

function canSeeBoothsFrom($boother) {
    if (isLoggedIn()) {
        return isAllowedToInteractWith($_SESSION['username'], $boother);
    }
    return false;
}
