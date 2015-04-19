<?php
/**
 * Created by PhpStorm.
 * User: Brad
 * Date: 4/25/14
 * Time: 9:50 PM
 */

require_once("{$_SERVER['DOCUMENT_ROOT']}/newbooth/email.php");

require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";
require_common("ImageUtils");
require_common("upload_utils");
require_common("internal_utils");
require_common("utils");

function doPostBooth($username, $rawImageBytes, $blurb, $friendsonly, $requestHash = null) {

    error_reporting(E_ALL);
    $dblink = connect_boothDB();

    $ratelimitsql = "
SELECT NOW( ) > datetime + INTERVAL 1
MINUTE AS timePassed
FROM `boothnumbers`
WHERE fkUsername = '".$dblink->real_escape_string($username)."'
ORDER BY datetime DESC
LIMIT 1 ";
    $result = $dblink->query($ratelimitsql);
    if (false && $result->num_rows != 0) { //TODO: Re-enable this for launch
        $rateLimitRow = $result->fetch_assoc();
        if ($rateLimitRow['timePassed'] == 0) {
            return json_encode(array(
                "error" => "User posting too rapidly"
            ));
        }
    }

    $uploadedfile = ImageUtils::makeFromEncoded($rawImageBytes);
    $extension = ImageUtils::getExtensionOfEncoded($rawImageBytes);
    $sql = "SELECT `nextIndex`
			FROM `logintbl`	
			WHERE `username` = '".$dblink->real_escape_string($username)."'
			LIMIT 1";
    $nextIndexRes = $dblink->query($sql);
    if (!$nextIndexRes || $nextIndexRes->num_rows == 0) {
        return json_encode(array(
            "error" => sql_death1($sql)
        ));
    }
    $row = $nextIndexRes->fetch_array();
    $nextIndex = $row['nextIndex'] + 1;

    $formattedblurb = handle_links($blurb);
    $formattedblurb = handle_mentions($formattedblurb);
    $formattedblurb = handle_hashtags($formattedblurb);

    $columns = "(`fkUsername`, `source`, `isPublic`, `blurb`)";
    $values = "('".$dblink->real_escape_string($username)."', 'newbooth/post_utils.php',".(!($dblink->real_escape_string($friendsonly))).", '".$dblink->real_escape_string($formattedblurb)."')";
    if ($requestHash != null) {
        $columns = "(`fkUsername`, `source`, `isPublic`, `blurb`, `requestHash`)";
        $values = "('".$dblink->real_escape_string($username)."', 'newbooth/post_utils.php',".(!($dblink->real_escape_string($friendsonly))).", '".$dblink->real_escape_string($formattedblurb)."', '".$dblink->real_escape_string($requestHash)."')";
    }

    $sql = "INSERT INTO
			`boothnumbers` 
			".$columns."
			VALUES 
			".$values.";";
    $insertres = $dblink->query($sql);
    if (!$insertres) {
        return json_encode(array(
            "error" => sql_death1($sql)
        ));
    }

    $sql = "SELECT
			`pkNumber` 
			FROM `boothnumbers` 
			WHERE `fkUsername` = '".$dblink->real_escape_string($username)."'
			ORDER BY `pkNumber` DESC LIMIT 1;";
    $query = $dblink->query($sql);
    if (!$query) {
        return json_encode(array(
            "error" => sql_death1($sql)
        ));
    }

    $row = $query->fetch_array();
    $number = $row['pkNumber'];

    $sql = "INSERT INTO 
			`booth_full_record_tbl` 
			(`fkUsername`, `fkNumber`) 
			VALUES 
			('".$dblink->real_escape_string($username)."', ".$dblink->real_escape_string($number).");";
    $recordres = $dblink->query($sql);
    if (!$recordres) {
        return json_encode(array(
            "error" => sql_death1($sql)
        ));
    }

    $sql = "UPDATE 
			`boothnumbers` 
			SET 
			`imageTitle` = md5('".getBoothSalt().$dblink->real_escape_string($number)."'),
			`filetype` = '".$dblink->real_escape_string($extension)."'
			WHERE `pkNumber` = ".$dblink->real_escape_string($number).";";
    $makehash = $dblink->query($sql);
    if (!$makehash) {
        rollback($number);
        return json_encode(array(
            "error" => sql_death1($sql)
        ));
    }

    $sql = "SELECT 
			`imageTitle` 
			FROM `boothnumbers` 
			WHERE `pkNumber` = ".$dblink->real_escape_string($number).";";
    $imgquery = $dblink->query($sql);
    if (!$imgquery) {
        rollback($number);
        return json_encode(array(
            "error" => sql_death1($sql)
        ));
    }

    $row = $imgquery->fetch_array();
    $name = $row['imageTitle'];

    $filename = "{$_SERVER['DOCUMENT_ROOT']}/booths/".$name.".".$extension;
    $putOk = file_put_contents($filename, $uploadedfile);
    if (!$putOk) {
        rollback($number);
        return json_encode(array(
            "error" => "There was a problem uploading the file"
        ));
    }

    list($width,$height)=getimagesize($filename);

    if ($width <= 0 || $height <= 0) {
        return json_encode(array(
            "error" => "This file appears to have a width or height of 0 pixels.  This is not supported."
        ));
    }

    $sql = "UPDATE `boothnumbers` SET 
			`imageHeightProp` = " . $height/$width . " 
			WHERE `pkNumber` = " . $dblink->real_escape_string($number) . ";";
    $propq = $dblink->query($sql);
    if (!$propq) {
        sql_death1($sql);
    }

    $smallResult = uploadSmall($name, $extension, $height/$width, $filename, $uploadedfile);

    if (isset($smallResult['error'])) {
        rollback($number);
        return json_encode($smallResult);
    }

    $tinyResult = uploadTiny($name, $extension, $height/$width, $filename, $uploadedfile);
    if (isset($tinyResult['error'])) {
        rollback($number);
        return json_encode($tinyResult);
    }

    //update mentions table if the blurb contained @mentions
    preg_match_all("/@([a-zA-Z0-9]+)/", $blurb, $mentions, PREG_PATTERN_ORDER);
    foreach ($mentions[1] as $mention) {
        if ($mention != $username) {
            $putmention = "REPLACE INTO 
							`mentionstbl` 
							(`fkMentionerName`, 
							`fkMentionedName`, 
							`fkIndex`, 
							`fkBoothNumber`) 
							VALUES 
							('".$dblink->real_escape_string($username)."',
							'".strtolower($dblink->real_escape_string($mention))."',
							-1, 
							".$dblink->real_escape_string($number).");";
            $mentionq = $dblink->query($putmention);
            if (!$mentionq) {
                sql_death1($putmention);
                break;
            }
        }
    }

    $sql = "INSERT INTO
			`activitytbl` 
			(`fkUsername`, 
			`fkIndex`, 
			`type`) 
			VALUES 
			('".$dblink->real_escape_string($username)."',
			".$dblink->real_escape_string($number).",
			'booth');";
    $activityq = $dblink->query($sql);
    if (!$activityq) {
        sql_death1($sql);
    }

    sendNewBoothEmail($username, $number);

    $sql = "UPDATE `logintbl` 
			SET `nextIndex` = ".($dblink->real_escape_string($nextIndex))."
			WHERE `username` = '".$dblink->real_escape_string($username)."';";
    if (!$dblink->query($sql)) {
        sql_death1($sql);
    }

    return json_encode(array(
        "success" => array(
            "message" => "The booth was uploaded successfully.",
            "boothnum" => $number,
            "boothUrl" => base()."/users/".$username."/".$number
        )
    ));
}

/**
 * @param $name
 * @param $extension
 * @param $width
 * @param $height
 * @param $filename
 * @param $uploadedfile
 * @return string
 */
function uploadTiny($name, $extension, $heightOverWidth, $filename, $uploadedfile)
{
    $resizedFilename = "{$_SERVER['DOCUMENT_ROOT']}/booths/tiny/" . $name . "." . $extension;
    return uploadResized($extension, $heightOverWidth, $filename, $uploadedfile, 80, $resizedFilename, 80);
}

/**
 * @param $name
 * @param $extension
 * @param $heightOverWidth
 * @param $filename
 * @param $uploadedfile
 * @return string
 */
function uploadSmall($name, $extension, $heightOverWidth, $filename, $uploadedfile)
{
    $resizedFilename = "{$_SERVER['DOCUMENT_ROOT']}/booths/small/" . $name . "." . $extension;
    return uploadResized($extension, $heightOverWidth, $filename, $uploadedfile, 260, $resizedFilename, 75);
}

function uploadResized($extension, $heightOverWidth, $filename, $uploadedfile, $newwidth, $filename1, $jpegQuality)
{
    if ($extension == 'jpg') {
        $normal = imagecreatefromjpeg($filename);
        $small = ImageUtils::resize($normal, $filename, $heightOverWidth, $newwidth);
        if (!$small) {
            death("Image resize failed for " . $filename1);
            file_put_contents($filename1, $uploadedfile);
            return array("warning" => "Image resize failed for " . $filename1, "filename" => $filename1);
        } else {
            if (!imagejpeg($small, $filename1, $jpegQuality)) {
                death("imagejpeg failed for " . $filename1);
                file_put_contents($filename1, $uploadedfile);
                return array("warning" => "imagejpeg failed for " . $filename1, "filename" => $filename1);
            }
            return array("success" => "uploaded ok", "filename" => $filename1);
        }
    } else if ($extension == 'png') {
        $normal = imagecreatefrompng($filename);
        $small = ImageUtils::resize($normal, $filename, $heightOverWidth, $newwidth);
        if (!$small) {
            death("Image resize failed for " . $filename1);
            file_put_contents($filename1, $uploadedfile);
            return array("warning" => "imagejpeg failed for " . $filename1, "filename" => $filename1);
        } else {
            if (!imagepng($small, $filename1, 4)) {
                death("imagepng failed for " . $filename1);
                file_put_contents($filename1, $uploadedfile);
                return array("warning" => "imagepng failed for " . $filename1, "filename" => $filename1);
            }
            return array("success" => "uploaded ok", "filename" => $filename1);
        }
    } else {
        file_put_contents($filename1, $uploadedfile);
        return array("success" => "uploaded ok", "filename" => $filename1);
    }
}
