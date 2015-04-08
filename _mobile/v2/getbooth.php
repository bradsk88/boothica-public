<?php

require_once("{$_SERVER['DOCUMENT_ROOT']}/_mobile/v2/meta/AbstractUserApiResponse.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/_mobile/utils.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/livefeed/utils.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/userpages/booth_utils.php");
require_common("db");
require_common("utils");

class GetBoothApiResponse extends AbstractUserApiResponse {

    /**
     * This should be implemented by descendants but should not be called directly.  Use runAndEcho.
     */
    protected function run($username)
    {
        $dblink = connect_boothDB();

        if (parameterIsMissingAndEchoFailureMessage("boothnum")) {
            return;
        }
        $boothnum = $_POST['boothnum'];

        $sql = getBoothSQL($boothnum);
        $result = $dblink->query($sql);

        if (!$result) {
            echo
            json_encode(array(
                "error" => mysql_death1($sql)
            ));
            return;
        }

        $array = $result->fetch_array();
        while($row = $array) {
            $boothername = $row['fkUsername'];
            $cansee = false;
            if (isBoothPublic($boothnum)) {
                $cansee = true;
            }
            $isBootherFollowingMe = isFriendOf($username, $boothername);
            if (!$cansee && $isBootherFollowingMe) {
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
            $query = sql_query($sql);
            $likes = 0;
            if($query) {
                $r = $query->fetch_array();
                if ($r['num']) {
                    $likes = $r['num'];
                }
            }


            $root = base();
            $imagePath = "/booths/" . $row['imageTitle'] . "." . $row['filetype'];
            $absoluteImageUrl = $root . $imagePath;
            if ($cansee) {
                $booth = array(
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
                    'isfriend' => $isBootherFollowingMe,
                    'is_current_user_following' => isFriendOf($boothername, $username),
                    'datetime' => $row['datetime'],
                    'hoursago' => $row['hours'],
                    'minutesago' => $row['minutes'],
                    'absoluteImageUrl' => $absoluteImageUrl,
                    'allowed' => $cansee,
                );
                echo json_encode(array(
                    "success" => $booth
                ));
                return;
            } else {
                $booth = array(
                    'boothnum' => $boothnum,
                    'userboothnum' => $row['userBoothNumber'],
                    'userboothcount' => $userboothcount,
                    'boothername' => $boothername,
                    'bootherdisplayname' => (string)getDisplayName($boothername),
                    'blurb' => "",
                    'imageHash' => "/media/private.jpg",
                    'imagePath' => "/media/private.jpg",
                    'imageProp' => $row['imageHeightProp'],
                    'firstnum' => $firstnum,
                    'lastnum' => $lastnum,
                    'prevnum' => $prevBooth,
                    'nextnum' => $nextBooth,
                    'likes' => 0,
                    'isfriend' => $isBootherFollowingMe,
                    'isCurrentUserFollowing' => isFriendOf($boothername, $username),
                    'datetime' => $row['datetime'],
                    'hoursago' => $row['hours'],
                    'minutesago' => $row['minutes'],
                    'absoluteImageUrl' => base()."/media/private.jpg",
                    'allowed' => false,
                );
                echo json_encode(array(
                    "success" => $booth
                ));
                return;
            }
        }
    }
}

if (!isset($_SESSION)) session_start();
error_reporting(E_ALL);

$page = new GetBoothApiResponse();
$page->runAndEcho();

