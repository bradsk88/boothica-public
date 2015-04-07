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
require_common("utils");

function doPostBooth($username, $rawImageBytes, $blurb, $friendsonly, $requestHash = null) {

    error_reporting(0);
    $link = connect_to_boothsite();
    if (isset($_SESSION['username']) && isDeveloper($_SESSION['username'])) {
        error_reporting(E_ALL);
    }

    $ratelimitsql = "
SELECT NOW( ) > datetime + INTERVAL 1
MINUTE AS timePassed
FROM `boothnumbers`
WHERE fkUsername = '".$username."'
ORDER BY datetime DESC
LIMIT 1 ";
    $result = sql_query($ratelimitsql);
    if ($result->num_rows != 0) {
        $rateLimitRow = $result->fetch_assoc();
        if ($rateLimitRow['timePassed'] == 0) {
            return array(0214150354, null); // User posting too rapidly
        }
    }

    $uploadedfile = ImageUtils::makeFromEncoded($rawImageBytes);
    $extension = ImageUtils::getExtensionOfEncoded($rawImageBytes);
    $sql = "SELECT `nextIndex`
			FROM `logintbl`	
			WHERE `username` = '".$username."'
			LIMIT 1";
    $nextIndexRes = mysql_query($sql);
    if (!$nextIndexRes || mysql_num_rows($nextIndexRes) == 0) {
        echo mysql_death1($sql);
        return array(-1, null);
    }
    $row = mysql_fetch_array($nextIndexRes);
    $nextIndex = $row['nextIndex'] + 1;

    $columns = "(`fkUsername`, `source`, `isPublic`)";
    $values = "('".$username."', 'newbooth/file_upload.php',".(!($friendsonly)).")";
    if ($requestHash != null) {
        $columns = "(`fkUsername`, `source`, `isPublic`, `requestHash`)";
        $values = "('".$username."', 'newbooth/file_upload.php',".(!($friendsonly)).", '".$requestHash."')";
    }

    $sql = "INSERT INTO
			`boothnumbers` 
			".$columns."
			VALUES 
			".$values.";";
    $insertres = sql_query($sql);
    if (!$insertres) {
        echo mysql_death1($sql);
        return array(-1, null);
    }

    mysqli_autocommit($link, true);

    $sql = "SELECT
			`pkNumber` 
			FROM `boothnumbers` 
			WHERE `fkUsername` = '".$username."' 
			ORDER BY `pkNumber` DESC LIMIT 1;";
    $query = mysql_query($sql);
    if (!$query) {
        echo mysql_death1($sql);
        return array(-1, null);
    }

    $row = mysql_fetch_array($query);
    $number = $row['pkNumber'];

    $sql = "INSERT INTO 
			`booth_full_record_tbl` 
			(`fkUsername`, `fkNumber`) 
			VALUES 
			('".$username."', ".$number.");";
    $recordres = mysql_query($sql);
    if (!$recordres) {
        echo mysql_death1($sql);
        return array(-1, null);
    }

    $sql = "UPDATE 
			`boothnumbers` 
			SET 
			`imageTitle` = md5('dagnytajjard".$number."'),
			`filetype` = '".$extension."'
			WHERE `pkNumber` = ".$number.";";
    $makehash = mysql_query($sql);
    if (!$makehash) {
        rollback($number);
        echo mysql_death1($sql);
        return array(-1, null);
    }

    $sql = "SELECT 
			`imageTitle` 
			FROM `boothnumbers` 
			WHERE `pkNumber` = ".$number.";";
    $imgquery = mysql_query($sql);
    if (!$imgquery) {
        rollback($number);
        echo mysql_death1($sql);
        return array(-1, null);
    }

    $row = mysql_fetch_array($imgquery);
    $name = $row['imageTitle'];

    $filename = "{$_SERVER['DOCUMENT_ROOT']}/booths/".$name.".".$extension;
    file_put_contents($filename, $uploadedfile);

    list($width,$height)=getimagesize($filename);

    if ($width <= 0 || $height <= 0) {
        echo "This file appears to have a width or height of 0 pixels.  This is not supported.";
        return array(-1, null);
    }

    $sql = "UPDATE `boothnumbers` SET 
			`imageHeightProp` = " . $height/$width . " 
			WHERE `pkNumber` = " . $number . ";";
    $propq = mysql_query($sql);
    if (!$propq) {
        mysql_death1($sql);
    }

    $filename1 = uploadSmall($name, $extension, $height/$width, $filename, $uploadedfile);

    $filename2 = uploadTiny($name, $extension, $height/$width, $filename, $uploadedfile);

    $webpagefile = "{$_SERVER['DOCUMENT_ROOT']}/users/".$username."/".$number.'.php';
    $fh = @fopen($webpagefile, "w") or die("can't open file");
    $stringdata = "<?PHP include(\"{\$_SERVER['DOCUMENT_ROOT']}/userpages/booth.php\");";
    $pagewrite = fwrite($fh, $stringdata);
    fclose($fh);
    if (!$pagewrite) {
        death("Failed to write page".$webpagefile);
        echo "Failed to write booth page.  Try again";
        return array(-1, null);
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
							('".$username."',
							'".strtolower($mention)."', 
							-1, 
							".$number.");";
            $mentionq = mysql_query($putmention);
            if (!$mentionq) {
                mysql_death1($putmention);
                break;
            }
        }
    }
    $blurb = $_POST['blurb'];
    $formattedblurb = handle_links($blurb);
    $formattedblurb = handle_mentions($formattedblurb);
    $formattedblurb = handle_hashtags($formattedblurb);

    $sql = "UPDATE 
				`boothnumbers` 
				SET 
				blurb = '".mysql_real_escape_string(preg_replace('/(\r\n|\n|\r)/','<br/>',$formattedblurb))."',
				`userBoothNumber` = ".$nextIndex."
				WHERE pkNumber = '".$number."';";
    $blurbq = mysql_query($sql);
    if (!$blurbq) {
        mysql_death1($sql);
        rollbackall($filename, $filename1, $filename2, $number);
        echo "Error code 8";
        return array(-1, null);
    }

    $sql = "INSERT INTO 
			`activitytbl` 
			(`fkUsername`, 
			`fkIndex`, 
			`type`) 
			VALUES 
			('".$username."', 
			".$number.", 
			'booth');";
    $activityq = mysql_query($sql);
    if (!$activityq) {
        mysql_death1($sql);
    }

    sendNewBoothEmail($username, $number);

    $sql = "UPDATE `logintbl` 
			SET `nextIndex` = ".($nextIndex)." 
			WHERE `username` = '".$username."';";
    if (!mysql_query($sql)) {
        mysql_death1($sql);
    }

    return array(0, $number);
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
function uploadTiny($name, $extension, $percent2, $filename, $uploadedfile)
{
    $filename2 = "{$_SERVER['DOCUMENT_ROOT']}/booths/tiny/" . $name . "." . $extension;
    if ($extension == 'jpg') {
        $normal = imagecreatefromjpeg($filename);
        $small = ImageUtils::resize($normal, $filename, $percent2, 80);
        if (!$small) {
            death("Image resize failed for " . $filename2);
            file_put_contents($filename2, $uploadedfile);
            return $filename2;
        } else {
            imagejpeg($small, $filename2, 85);
            return $filename2;
        }
    } else if ($extension == 'png') {
        $normal = imagecreatefrompng($filename);
        $small = ImageUtils::resize($normal, $filename, $percent2, 80);
        if (!$small) {
            death("Image resize failed for " . $filename2);
            file_put_contents($filename2, $uploadedfile);
            return $filename2;
        } else {
            imagepng($small, $filename2, 4);
            return $filename2;
        }
    } else {
        file_put_contents($filename2, $uploadedfile);
        return $filename2;
    }
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
    $filename1 = "{$_SERVER['DOCUMENT_ROOT']}/booths/small/" . $name . "." . $extension;
    if ($extension == 'jpg') {
        $normal = imagecreatefromjpeg($filename);
        $small = ImageUtils::resize($normal, $filename, $heightOverWidth, 260);
        if (!$small) {
            death("Image resize failed for " . $filename1);
            file_put_contents($filename1, $uploadedfile);
            return $filename1;
        } else {
            if (!imagejpeg($small, $filename1, 75)) {
                death("imagejpeg failed for " . $filename1);
                file_put_contents($filename1, $uploadedfile);
                return $filename1;
            }
            return $filename1;
        }
    } else if ($extension == 'png') {
        $normal = imagecreatefrompng($filename);
        $small = ImageUtils::resize($normal, $filename, $heightOverWidth, 260);
        if (!$small) {
            death("Image resize failed for " . $filename1);
            file_put_contents($filename1, $uploadedfile);
            return $filename1;
        } else {
            if (!imagepng($small, $filename1, 4)) {
                death("imagepng failed for " . $filename1);
                file_put_contents($filename1, $uploadedfile);
                return $filename1;
            }
            return $filename1;
        }
    } else {
        file_put_contents($filename1, $uploadedfile);
        return $filename1;
    }
}
