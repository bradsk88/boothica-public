<?php

require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";
require_once("{$_SERVER['DOCUMENT_ROOT']}/newbooth/post_utils.php");
require_common("cookies");
require_common("utils");
require_common("universal_utils");

function rollback($number) {

    $dblink = connect_boothDB();
	$sql = "DELETE FROM 
			`boothnumbers` 
			WHERE `pkNumber`  = ".$number.";";
	$deleteres = $dblink->query($sql);
	if (!$deleteres) {
		sql_death1($sql);
		return -1;
	}
	return 0;

}

function rollbackall($filename, $filename1, $filename2, $number) {
	$un1 = unlink( $filename );
	$un2 = unlink( $filename1 );
	$un3 = unlink( $filename2 );
	if (!$un1) {
		death("Failed to delete ".$filename);
	}
	if (!$un2) {
		death("Failed to delete ".$filename1);
	}
	if (!$un3) {
		death("Failed to delete ".$filename2);
	}
	rollback($number);
	echo "Failed to create this booth.";
}
