<?php

/**
 * Contains boilerplate code for writing cleaner code.  Hopefully.
 */

require_once "{$_SERVER['DOCUMENT_ROOT']}/common/universal_utils.php";

function base() {
    return "http://boothi.ca";
}

function require_common( $asset ) {
    require_once "{$_SERVER['DOCUMENT_ROOT']}/common/".$asset.".php";
}

function require_account_asset( $asset ) {
    require_once "{$_SERVER['DOCUMENT_ROOT']}/account/assets/".$asset.".php";
}

function require_asset( $asset ) {
    require_once "{$_SERVER['DOCUMENT_ROOT']}/common/assets/".$asset.".php";
}

function require_mod_asset( $asset ) {
    require_once "{$_SERVER['DOCUMENT_ROOT']}/mod_assets/".$asset.".php";
}

function require_page( $asset ) {
    require_once "{$_SERVER['DOCUMENT_ROOT']}/pages/".$asset.".php";
}

function include_error( $page ) {
    include "{$_SERVER['DOCUMENT_ROOT']}/errors/".$page.".php";
}