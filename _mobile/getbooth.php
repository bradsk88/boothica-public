<?php

session_start();
error_reporting(0);
main();

function main() {

    require_once("{$_SERVER['DOCUMENT_ROOT']}/userpages/booth_utils.php");
    require_once("{$_SERVER['DOCUMENT_ROOT']}/livefeed/utils.php");
    require_once("{$_SERVER['DOCUMENT_ROOT']}/_mobile/utils.php");
    require_once("{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php");
    require_common("db");
    require_common("utils");

    $link = connect_to_boothsite();
    update_online_presence();

    $username = $_POST['username'];
    if (isset($_SESSION['username'])) {
        $username = $_SESSION['username'];
    }
    else if (isset($_POST['username']) && failsStandardMobileChecksAndEchoFailureMessage()) {
        return;
    }

    if (parameterIsMissingAndEchoFailureMessage("boothnum")) {
        return;
    }
    $boothnum = $_POST['boothnum'];

    $sql = getBoothSQL($boothnum);
    $result = mysql_query($sql);

    if (!$result) {
        echo
        json_encode(array(
            "error" => mysql_death1($sql)
        ));
        return;
    }

    $booths = array();
    while($row = mysql_fetch_array($result)) {
        $boothername = $row['fkUsername'];
        $cansee = false;
        if (isBoothPublic($boothnum)) {
            $cansee = true;
        }
        if (!$cansee && isFriendOf($username, $boothername)) {
            $cansee = true;
        }

        $query = sql_query("SELECT count(*) as num FROM `boothnumbers` WHERE `fkUsername` = '".$boothername."' AND `pkNumber` > ".$boothnum.";");
        $offset = sql_get_expectOneRow($query, "num");
        $query = sql_query("SELECT count(*) as num FROM `boothnumbers` WHERE `fkUsername` = '".$boothername."';");
        $userboothcount = sql_get_expectOneRow($query, "num") - $offset;

        $prevBooth = getPreviousBoothNumber($boothnum, $boothername);
        $nextBooth = getNextBoothNumber($boothnum, $boothername);
        $firstnum = getFirstBoothNumber($boothername);
        $lastnum = getLastBoothNumber($boothername);

        $sql = "SELECT SUM(`value`) as `num` FROM `likes_boothstbl`
			WHERE `fkBoothNumber` = ".$boothnum.";";
        $query = mysql_query($sql);
        $likes = 0;
        if($query) {
            $r = mysql_fetch_array($query);
            $likes = $r['num'];
        }


        $root = "http://" . $_SERVER['SERVER_NAME'];
        $imagePath = "/booths/" . $row['imageTitle'] . "." . $row['filetype'];
        if ($cansee) {
            $booths[] = array(
                'boothnum' => $boothnum,
                'userboothnum' => $row['userBoothNumber'],
                'userboothcount' => $userboothcount,
                'boothername' => $boothername,
                'bootherdisplayname' => (string)getDisplayName($boothername),
                'blurb' => $row['blurb'],
                'imageHash' => $row['imageTitle'],
                'imagePath' => $imagePath,
                'imageProp' => $row['imageHeightProp'],
                'firstnum' => $firstnum,
                'lastnum' => $lastnum,
                'prevnum' => $prevBooth,
                'nextnum' => $nextBooth,
                'likes' => $likes,
                'isfriend' => isFriendOf($username, $boothername),
                'datetime' => $row['datetime'],
                'hoursago' => $row['hours'],
                'minutesago' => $row['minutes'],
                'absoluteImageUrl' => $root.$imagePath
            );
            echo json_encode($booths);
            return;
        } else {
            $booths[] = array(
                'boothnum' => $boothnum,
                'userboothnum' => $row['userBoothNumber'],
                'userboothcount' => $userboothcount,
                'boothername' => $boothername,
                'bootherdisplayname' => (string)getDisplayName($boothername),
                'blurb' => "This booth is private",
                'imageHash' => "/media/private.jpg",
                'imagePath' => "/media/private.jpg",
                'imageProp' => $row['imageHeightProp'],
                'firstnum' => $firstnum,
                'lastnum' => $lastnum,
                'prevnum' => $prevBooth,
                'nextnum' => $nextBooth,
                'likes' => 0,
                'isfriend' => isFriendOf($username, $boothername),
                'datetime' => $row['datetime'],
                'hoursago' => $row['hours'],
                'minutesago' => $row['minutes'],
                'absoluteImageUrl' => base()."/media/private.jpg"
            );
            echo json_encode($booths);
            return;
        }
    }

}

