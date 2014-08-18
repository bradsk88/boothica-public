<?php

const GOAL = 127.67; //TODO: Can this var go somewhere else?

session_start();
error_reporting(0);
main();

function main() {

    require_once("{$_SERVER['DOCUMENT_ROOT']}/userpages/booth_utils.php");
    require_once("{$_SERVER['DOCUMENT_ROOT']}/livefeed/utils.php");
    require_once("{$_SERVER['DOCUMENT_ROOT']}/_mobile/utils.php");
    require_once("{$_SERVER['DOCUMENT_ROOT']}/donations/DonationInfo.php");
    require_once("{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php");
    require_common("db");
    require_common("utils");

    $link = connect_to_boothsite();
    update_online_presence();

   $username = $_POST['username'];
    if (isset($_SESSION['username'])) {
        $username = $_SESSION['username'];
    }
    if (isset($_POST['username']) && failsStandardMobileChecksAndEchoFailureMessage()) {
        return;
    }

    $booths = array();


    if (false) { //Donations being requested

        $donationInfo = new DonationInfo();
        if ($donationInfo->loadSucceeded()) {
            $newPct = (int)((((double)$donationInfo->getRaisedDollars()+25)/GOAL)*100);
            $booths[] = array(
                'message' => "A donation of $25 will move us from ".$donationInfo->getPercentRaisedOf(GOAL)."% to ".$newPct."%.  Please Donate.",
//            'message' => "We met our donation goal.  Thanks everybody!",
            'url' => "/info/donations",
            'severity' => 'normal');

        }
    }

    if (getNews() > 0) {
        $booths[] = array(
            'message' => "There is news!",
            'url' => "/info/news",
            'severity' => 'low');
    }

    $sql = "SELECT `message`, `expiry` < NOW( ) AS `old`
            FROM `sitenoticestbl`
            ORDER BY `pkNum` DESC
            LIMIT 1";
    $result = mysql_query($sql);

    if (!$result) {
        echo mysql_death1($sql);
        return;
    }

    while($row = mysql_fetch_array($result)) {
        if ($row['old']) {
            continue;
        }
        $booths[] = array(
            'message' => $row['message'],
            'url' => $row['url'],
            'severity' => 'low');
    }

    if (!isset($username) || $username == null) {
        echo json_encode($booths);
        return;
    }

    if (hasNoEmail($username)) {
        $booths[] = array(
            'message' => 'Your account has problems:Click here to fix them',
            'url' => '/account/changeemail',
            'severity' => 'high');
    }

    if (hasNoSecurity($username)) {
        $booths[] = array(
            'message' => 'Your account has problems:Click here to fix them',
            'url' => '/account/changesecurity',
            'severity' => 'high');
    }

    echo json_encode($booths);
}

function getNews() {

    $sql = "SELECT COUNT(*) AS `num` FROM `sitemsgtbl`
            WHERE `fkUsername` = '".$_SESSION['username']."' AND `area` = 'news';";
    $result = mysql_query($sql);
    if (!$result) {
        mysql_death1($sql);
    }

    $row = mysql_fetch_array($result);
    return $row['num'];

}