<?php

require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";
require_common("db");
require_common("utils");

function doesBoothBelongTo($number, $boother) {
    $sql = "SELECT true FROM `boothnumbers`
            WHERE `fkUsername` = '".$boother."'
            AND `pkNumber` = ".$number." LIMIT 1;";
    $query = sql_query($sql);
    if (emptyResult($query)) {
        return false;
    }
    return true;
}

function getBoothOwner($number) {
    $sql = "SELECT `fkUsername` FROM `boothnumbers`
            WHERE `pkNumber` = ".$number." LIMIT 1;";
    $query = sql_query($sql);
    return sql_get_expectOneRow($query, "fkUsername");
}

function isAllowedToInteractWithBooth($username, $boothnum) {
    if (isBanned($username)) {
        return false;
    }
    if (isSuspended($username)) {
        return false;
    }
    $boothowner = getBoothOwner($boothnum);
    if (doesUserAppearPrivate($boothowner)) {
        return false;
    }
    return true;
}
