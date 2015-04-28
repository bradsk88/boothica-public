<?php

require_once("{$_SERVER['DOCUMENT_ROOT']}/_mobile/v2/meta/AbstractPublicApiResponse.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/_mobile/utils.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/booth/utils.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/userpages/booth_utils.php");
require_common("db");
require_common("utils");

class GetBoothApiResponse extends AbstractPublicApiResponse {

    /**
     * This should be implemented by descendants but should not be called directly.  Use runAndEcho.
     */
    protected function runMaybeLoggedIn()
    {
        $dblink = connect_boothDB();

        if (parameterIsMissingAndEchoFailureMessage("boothnum")) {
            return;
        }
        $boothnum = $_POST['boothnum'];

        $sql = getBoothSQL($boothnum);
        $result = $dblink->query($sql);

        if (!$result) {
            $this->markCallAsFailure(sql_death1($sql));
            return;
        }

        while($row = $result->fetch_array()) {
            $boothername = $row['fkUsername'];
            $cansee = false;
            if (!doesUserAppearPrivate($boothername)) {
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


            $isBootherFollowingMe = isFriendOf(parent::getUsernameIfSet(), $boothername);
            $root = base();
            $imagePath = "/booths/" . $row['imageTitle'] . "." . $row['filetype'];
            $absoluteImageUrl = $root . $imagePath;

            if (!doesUserAppearPrivate($boothername)) {
                $cansee = true;
            }

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
                    'is_current_user_following' => isFriendOf($boothername, parent::getUsernameIfSet()),
                    'datetime' => $row['datetime'],
                    'hoursago' => $row['hours'],
                    'minutesago' => $row['minutes'],
                    'absoluteImageUrl' => $absoluteImageUrl,
                    'allowed' => $cansee,
                );
                $this->markCallAsSuccessful("Booth get OK", $booth);
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
                    'isCurrentUserFollowing' => isFriendOf($boothername, parent::getUsernameIfSet()),
                    'datetime' => $row['datetime'],
                    'hoursago' => $row['hours'],
                    'minutesago' => $row['minutes'],
                    'absoluteImageUrl' => base()."/media/private.jpg",
                    'allowed' => false,
                );
                $this->markCallAsSuccessful("Booth get OK", $booth);
                return;
            }
        }
    }
}

if (!isset($_SESSION)) session_start();
error_reporting(E_ALL);

$page = new GetBoothApiResponse();
$page->runAndEcho();

