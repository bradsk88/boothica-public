<?php

session_start();
require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";
require_common("cookies");
require_common("utils");
require_common("universal_utils");
require_once("{$_SERVER['DOCUMENT_ROOT']}/newbooth/post_utils.php");
$link = connect_to_boothsite();

if (isset($_SESSION['username'])) {
	main();
} else if (isset($_COOKIE['userid'])) {
	$cookieset = cookie_set();
	if ($cookieset == 0) {
		main();
	} else {
		echo "Your session timed out.  Please ensure your browser is not blocking cookies.  Error code[".$cookieset."]";
	}
} else {
	echo "You must be logged in to post booths.";
}	


function file_upload_error_message($error_code) {
    switch ($error_code) {
        case UPLOAD_ERR_INI_SIZE:
            return 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
        case UPLOAD_ERR_FORM_SIZE:
            return 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
        case UPLOAD_ERR_PARTIAL:
            return 'The uploaded file was only partially uploaded';
        case UPLOAD_ERR_NO_FILE:
            return 'No file was uploaded';
        case UPLOAD_ERR_NO_TMP_DIR:
            return 'Missing a temporary folder';
        case UPLOAD_ERR_CANT_WRITE:
            return 'Failed to write file to disk';
        case UPLOAD_ERR_EXTENSION:
            return 'File upload stopped by extension';
        default:
            return 'Unknown upload error';
    }
} 

function rollback($number) {

	$sql = "DELETE FROM 
			`boothnumbers` 
			WHERE `pkNumber`  = ".$number.";";
	$deleteres = mysql_query($sql);
	if (!$deleteres) {
		mysql_death1($sql);
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

function main() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        print404();
        return;
    }

    if (!isset($_SESSION['username'])) {
        echo "Session expired.  Copy your blurb and refresh, please report this.";
        return;
    }

    if (!($_SERVER['REQUEST_METHOD'] === 'POST')) {
        echo "This page was not meant to be accessed this way";
        return;
    }

    if (!isset($_POST['image']) || !isset($_POST['blurb']))  {
        doIA();
        return;
    }
    $username = strtolower($_SESSION['username']);

    $friendsonly = false;
    if (isset($_POST['friendsonly'])) {
        $friendsonly = $_POST['friendsonly'];
    }

    list($success, $number) = doPostBooth($username, $_POST['image'], $_POST['blurb'], $friendsonly);

    if ($success == 0) {
	    go_to("users/".$_SESSION['username']."/".$number );
    }
}


function doIA() {
	include("{$_SERVER['DOCUMENT_ROOT']}/content/html.php");
	include("{$_SERVER['DOCUMENT_ROOT']}/content/top.php");
	include("{$_SERVER['DOCUMENT_ROOT']}/common/universal_utils.php");
	printIA();
	include("{$_SERVER['DOCUMENT_ROOT']}/content/bottom.php");
}
