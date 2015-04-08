<?PHP

// register_shutdown_function('errorHandler');
// function errorHandler() {

// $err = error_get_last();
// if($err) {
// if (isset($_SESSION['username']) && isModerator($_SESSION['username'])) {
// echo death($err['type']."\n".$err['message']."\n".$err['file']."\n".$err['line']."\n");
// } else {
// death($err['type']."\n".$err['message']."\n".$err['file']."\n".$err['line']."\n");
// echo "<script type = 'text/javascript'>location.href = '/errors/error.php?msg=An%20unexpected%20error%20has%20occurred';</script>";
// }
// }
// }

require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";
require_asset('DisplayName'); require_asset('UserIcon');
require_common('db');
require_common('db_auth');

function checkNotNull($obj) {
    checkNotNull_Msg($obj, "Null Pointer Exception");
}

function checkNotNull_Msg($obj, $msg) {
    if ($obj == null) {
        death($msg);
        throw new Exception("Null Pointer");
    }
}

function useJQuery() {
    echo jQueryString();
}

function jQueryString() {
    return "
			<script class=\"jsbin\" src=\"http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.js\"></script>
	";
}

function get_ip_address() {
    foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            foreach (explode(',', $_SERVER[$key]) as $ip) {
                if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
                    return $ip;
                }
            }
        }
    }
}

function removeSiteMsg($type) {
    if (isset($_SESSION['username'])) {
        $sql = "DELETE FROM `sitemsgtbl` WHERE `fkUsername` = '".$_SESSION['username']."' AND `area` = '".$type."';";
        $result = mysql_query($sql);
        if (!$result) {
            mysql_death1($sql);
        }
    }
}

function go_home() {

    echo "<script = 'text/javascript'>location.href='/';</script>";

}

function go_to($relativepage) {

    echo "<script = 'text/javascript'>location.href='/".$relativepage."';</script>";

}

function go_to_and_post($relativepage, $vars, $fallbackmessage) {

    echo "
    <form action = \"/".$relativepage."\" method = \"post\" name = \"form\">
    ";
    foreach ($vars as $a => $b) {
        echo "<input type='hidden' name='".$a."' value='".urlencode($b)."'>";
    }
    echo "
    </form>
    ".$fallbackmessage."
    <script = 'text/javascript'>document.form.submit()</script>
    ";

}

function go_to_404() {

    echo "<script = 'text/javascript'>location.href='/errors/404page';</script>";

}

function go_to_login() {
    echo "<script = 'text/javascript'>location.href='/errors/loginpage';</script>";
}

function go_to_banned() {
    echo "<script = 'text/javascript'>location.href='/errors/bannedpage';</script>";
}

function go_to_suspended() {

    echo "<script = 'text/javascript'>location.href='/errors/suspendedpage';</script>";

}

function go_to_unexpected_error() {

    if (isset($down) && $down) {
        return;
    }
    echo "<script = 'text/javascript'>location.href='/errors/error?msg=An%20unexpected%20error%20has%20occurred;</script>";

}

function go_to_db_error($sql) {
    if (isset($down) && $down) {
        echo(sql_error($sql));
        return;
    }
    if (isset($_SESSION['username']) && isModerator($_SESSION['username'])) {
        go_to_error($sql);
    } else {
        go_to_error(sql_death1($sql));
    }
}

function go_to_error_return($msg, $return) {
    echo "<script = 'text/javascript'>location.href='/errors/error?msg=".urlencode($msg)."&return=".$return."';</script>";
}

function go_to_error($msg) {
    if ($down) {
        echo($msg);
        return;
    }
    echo "<script = 'text/javascript'>location.href='/errors/error?msg=".urlencode($msg)."';</script>";
}

include("{$_SERVER['DOCUMENT_ROOT']}/utils/devlist.php");

function mysql_death2($link,$sql) {
    $usern = "not logged in";
    if (isset($_SESSION['username'])) {
        $usern = $_SESSION['username'];
    }
    foreach (getDevs() as $dev) {
        error_log("You are receiving this because you are on the developers list\n\nUsername at time of death: ".$usern."\nRequest page: ".$_SERVER['REQUEST_URI']."\nScript page: ".__FILE__.": \n".mysql_error($link)."\n".$sql."\n\n".get_ip_address(), 1, $dev);
    }
    return "Database error.";
}

function mysql_deathm($sql, $msg) {
    if ($down) {
        echo(mysql_error($sql));
        return;
    }
    $usern = "not logged in";
    if (isset($_SESSION['username'])) {
        $usern = $_SESSION['username'];
    }
    foreach (getDevs() as $dev) {
        error_log("You are receiving this because you are on the developers list\n\n"."MySQL Death\nUsername at time of death: ".$usern."\nRequest page: ".$_SERVER['REQUEST_URI']."\nScript page: ".__FILE__.": \n".mysql_error()."\n".$sql."\n".$msg."\n\n".get_ip_address(), 1, $dev);
    }
    return "Database error.";
}

function mysql_death1($sql) {
    //TODO: Re-enable
    return;
    if (isset($down) && $down) {
        echo(mysql_error($sql));
        return "Site is down";
    }
    $usern = "not logged in";
    if (isset($_SESSION['username'])) {
        $usern = $_SESSION['username'];
    }
    ob_start();
    debug_print_backtrace();
    $trace = ob_get_clean();
    $old_error = mysql_error();
    if (isset($link)) {
        $new_error = mysqli_errno($link).": ".mysqli_error($link);
    }

    foreach (getDevs() as $dev) {
        error_log("You are receiving this because you are on the developers list\n\n"."MySQL Death\nUsername at time of death: "
            .$usern."\nRequest page: ".$_SERVER['REQUEST_URI']."\nScript page: ".__FILE__.": \nMySQL ".$old_error."\nMySQLi ".$new_error."\n\nSQL:".$sql.get_ip_address()."\n\n".$trace, 1, $dev);
    }
    return "Database error.";
}

function mysql_death($error) {
    $usern = "not logged in";
    if (isset($_SESSION['username'])) {
        $usern = $_SESSION['username'];
    }
    foreach (getDevs() as $dev) {
        error_log("You are receiving this because you are on the developers list\n\nUsername at time of death: ".$usern."\nRequest page: ".$_SERVER['REQUEST_URI']."\nScript page: ".__FILE__.": \n".$error.get_ip_address(), 1, $dev);
    }
    return "Database error.";
}

function death($error) {
    $usern = "not logged in";
    if (isset($_SESSION['username'])) {
        $usern = $_SESSION['username'];
    }
    ob_start();
    debug_print_backtrace();
    $trace = ob_get_clean();
    error_log($error);
    foreach (getDevs() as $dev) {
//        error_log("You are receiving this because you are on the developers list\n\nUsername at time of death: ".$usern.
//            "\nRequest page: ".$_SERVER['REQUEST_URI']."\nScript page: ".__FILE__.": \n".$error."\n\n".get_ip_address()."\n\n".$trace, 1, $dev);
    }
    return "Internal error.";
}


function debug($text) {

    if (isset($_SESSION['debugon']) && $_SESSION['debugon']  == true) {
        echo "Debug:<p>".$text."<p/>";
    }

}

function record_ip($type) {

    $dblink = connect_boothDB();
    $sql = "INSERT INTO
			`hackattemptstbl`
			(`ip`, `type`)
			VALUES
			('".get_ip_address()."', '".$type."');";
    $result = $dblink->query($sql);
    if (!$result) {
        sql_death1("Hack report is broken. IP: ".get_ip_address()." TYPE: ".$type);
    }

}

function update_online_presence() {

    if (isset($_SESSION['username'])) {
        $username = $_SESSION['username'];
        $sql = "UPDATE
					`logintbl`
					SET
					`lastonline` = current_timestamp
					WHERE `username` = '".$username."';";
        sql_query($sql);
    }
}

function getDisplayName($username) {
    return new DisplayName($username);
}

function isPublic($username) {

    $sql = "SELECT
			`username`,
			`password`
			FROM `logintbl`
			WHERE `username` = '".$username."'
			LIMIT 1;";
    $dblink = connect_boothDB();
    $result = $dblink->query($sql);
    if (!$result) {
        sql_death1($sql);
        return false;
    }

    $loginarray= $result->fetch_array();
    $passwordhash = $loginarray['password'];

    $sql = "SELECT `fkPassword`
			FROM `userspublictbl`
			WHERE `fkUsername` = '" . $username . "'
			LIMIT 1;";
    $result2 = $dblink->query($sql);
    if (!$result2) {
        sql_death1($sql);
        return false;
    }
    $numrows = $result->num_rows;
    if ($numrows == 1) {
        $publichasharray = $result2->fetch_array();
        $publichash = $publichasharray['fkPassword'];
    } else {
        if ($result2->num_rows > 1) {
            record_ip('".$commentername.": muliple entries in public table');
        }
        return false;
    }

    if ( $publichash == $passwordhash) {
        return true;
    }
    return false;

}

function mutualFriends($user1,$user2) {

    if ($user1 == "") {
        death("mutualFriends: blank 1st parameter");
        return false;
    }

    if ($user2 == "") {
        death("mutualFriends: blank 2nd parameter");
        return false;
    }

    $sql = "SELECT
			true
			FROM `friendstbl`
			WHERE `fkUsername` = '".$user1."'
			AND `fkFriendName` = '" . $user2 . "'
			AND (SELECT true FROM `friendstbl` WHERE
				`fkUsername` = '".$user2."'
				AND `fkFriendName` = '".$user1."'
				LIMIT 1)
			LIMIT 2;";
    $result = mysql_query($sql);
    if ($result) {
        $num = mysql_num_rows($result);
        if ($num == 1) {
            return true;
        } else if ($num > 1) {
            record_ip("friendship ".$user1."->".$user2." exists in database more than once.");
        }
    } else {
        mysql_death1($sql);
    }
    return false;

}

function setDefaultPage($page, $username) {
    if ($page == "ALL") {
        $defaultpage = "/";
    } else if ($page == "BOOTHS") {
        $defaultpage = "/users/".$username."/friendsbooths";
    } else if ($page == "TODAY") {
        $defaultpage = "/users/".$username."/today";
    }
    setcookie('defaultpage',$defaultpage, time() + 30*24*60*60, "/");
}

function isFriendOf($user1,$user2) {

    if (!isset($user1) ||$user1 == null || !isset($user2) || $user2 == null) {
        return false;
    }
    if ($user1 == $user2) {
        return true;
    }
    $dblink = connect_boothDB();
    $sql = "SELECT
			true
			FROM `friendstbl`
			WHERE `fkUsername` = '".$user2."'
			AND `fkFriendName` = '" . $user1 . "'
			LIMIT 2;";
    $result = $dblink->query($sql);
    if ($result) {
        $num = $result->num_rows;
        if ($num == 1) {
            return true;
        } else if ($num > 1) {
            record_ip("friendship ".$user1."->".$user2." exists in database more than once.");
        }
    } else {
        sql_death1($sql);
    }
    return false;

}

function isIgnoring($user1,$user2) {

    $sql = "SELECT
			true
			FROM `ignorestbl`
			WHERE `fkUsername` = '".$user1."'
			AND `fkIgnoredName` = '" . $user2 . "'
			LIMIT 2;";
    $result = mysql_query($sql);
    if ($result) {
        $num = mysql_num_rows($result);
        if ($num == 1) {
            return true;
        } else if ($num > 1) {
            record_ip("ignoreship ".$user1."->".$user2." exists in database more than once.");
        }
    } else {
        mysql_death1($sql);
    }
    return false;

}

function isAllowedToInteractWith($viewingUser,$otherUser) {
    if (isFriendOf($viewingUser, $otherUser)) {
        return true;
    }
    if (isPublic($otherUser)) {
        return true;
    }
    return false;
}

function userExists($username) {
    if (strlen($username) == 0) {
        return false;
    }
    if (!isset($dblink)) $dblink = connect_boothDB();
    $sql = "SELECT `username` FROM `logintbl` WHERE `username` = '".$username."' LIMIT 1";
    return !emptyResult(sql_query($sql));
}

function isBanned($username) {

    $dblink = connect_boothDB();
    $sql = "SELECT `fkUsername` FROM `usersbannedtbl` WHERE `fkUsername` = '".$username."' LIMIT 2";
    $result = sql_query($sql);
    if ($result) {
        $num = $result->num_rows;
        if ($num == 1) {
            return true;
        } else if ($num == 0) {
            return false;
        } else {
            death("Multiple entries in usersbannedtbl.  Name: ".$username.", IP:".get_ip_address());
            return false;
        }
    } else {
        sql_death1($sql);
        return false;
    }

}

function isIndividualBoothPrivate($boothnumber) {

    $sql = "SELECT `isPublic` FROM `boothnumbers` WHERE `pkNumber` = ".$boothnumber." LIMIT 1;";
    $result = sql_query($sql);
    $row = sql_get_expectOneRow($result, "isPublic");
    if ($row == true) {
        return false;
    }
    return true;
}

function isBoothPublic($boothnumber) {

    $dblink = connect_boothDB();

    if (isIndividualBoothPrivate($boothnumber)) {
        return false;
    }
    $sql = "SELECT `fkUsername`
			FROM `userspublictbl`
			WHERE `fkUsername` =
				(SELECT `fkUsername`
					FROM `boothnumbers`
					WHERE `pkNumber` = ".$boothnumber."
					LIMIT 1 )
			LIMIT 2";
    $result = $dblink->query($sql);
    if ($result) {
        $num = $result->num_rows;
        if ($num == 1) {
            return true;
        } else if ($num == 0) {
            return false;
        } else {
            death("Multiple entries in userspublictbl.  Booth Number: ".$boothnumber.", IP:".get_ip_address());
            return false;
        }
    } else {
        sql_death1($sql);
        return false;
    }
}

function isSuspended($username) {

    $dblink = connect_boothDB();
    $sql = "SELECT `fkUsername` FROM `userssuspendedtbl` WHERE `fkUsername` = '".$username."' LIMIT 2";
    $result = $dblink->query($sql);
    if ($result) {
        $num = $result->num_rows;
        if ($num == 1) {
            return true;
        } else if ($num == 0) {
            return false;
        } else {
            death("Multiple entries in userssuspendedtbl.  Name: ".$username.", IP:".get_ip_address());
            return true;
        }
    } else {
        sql_death1($sql);
        return false;
    }

}

function doBanSuspendCheck($username) {

    if (isBanned($username)) {
        go_to_banned();
        return false;
    } else if (isSuspended($username)){
        go_to_suspended();
        return false;
    }
    return true;

}

function getUserNumber($username) {
    if (isset($_SESSION['usernum'])) {
        return $_SESSION['usernum'];
    }
    $sql = "SELECT `usernumber` FROM `logintbl`
            WHERE `username` = '".$username."'
            LIMIT 2;";
    $query = sql_query($sql);
    if ($query===false) {
        return;
    }
    $row = $query->fetch_assoc();
    return $row['usernumber'];
}


function getPublicImage($username) {

    // if (isset($_SESSION['username']) && (mutualFriends($_SESSION['username'], $username) || isPublic($username))) {

    // $sql = "SELECT
    // `imageTitle`,
    // `filetype`
    // FROM `boothnumbers`
    // WHERE fkUsername = '".$username."'
    // ORDER BY `pkNumber` DESC
    // LIMIT 1;";
    // $result = mysql_query($sql);
    // if (!$result) {
    // mysql_death1($sql);
    // return "/media/error.png";
    // }
    // if (mysql_num_rows($result) == 0) {
    // return "/media/noimage.jpg";
    // } else {
    // $row = mysql_fetch_array($result);
    // return "/booths/".$row['imageTitle'].".".$row['filetype'];
    // }
    // } else {
    $sql = "SELECT
				`hasIcon`, `iconext`
				FROM `logintbl`
				WHERE `username` = '".$username."'
				LIMIT 2;";
    $result = mysql_query($sql);
    if (!$result) {
        mysql_death1($sql);
        return "/media/error.png";
    }

    $num = mysql_num_rows($result);
    if ($num == 1) {
        $row = mysql_fetch_array($result);
        if ($row['hasIcon'] == 1) {
            return "/users/".$username."/public.".$row['iconext'];
        } else {
            return "/media/private.jpg";
        }
    } else {
        death($num." rows in logintbl for user: ".$username);
        return "/media/error.png";
    }
    // }

}

function getDefaultLayout($username) {
    $sql = "SELECT `defaultPage` FROM `logintbl` WHERE `username` = '".$username."' LIMIT 1;";
    $result = mysql_query($sql);
    if (!$result) {
        mysql_death1($sql);
        return "ERROR";
    }

    $row = mysql_fetch_array($result);
    $page = $row['defaultPage'];
    if ($page == "ALL") {
        return "All Activity";
    } else if ($page == "BOOTHS") {
        return "Booths Only";
    } else if ($page == "TODAY") {
        return "Today At A Glance";
    } else {
        return "ERROR";
        death("User ".$username." has strange default page: ".$page);
    }

}


function getLayoutImage($username) {
    $sql = "SELECT `defaultPage` FROM `logintbl` WHERE `username` = '".$username."' LIMIT 1;";
    $result = mysql_query($sql);
    if (!$result) {
        mysql_death1($sql);
        return "ERROR";
    }

    $row = mysql_fetch_array($result);
    $page = $row['defaultPage'];
    if ($page == "ALL") {
        return "/registration/activity.png";
    } else if ($page == "BOOTHS") {
        return "/registration/booths.png";
    } else if ($page == "TODAY") {
        return "/registration/today.png";
    } else {
        return "/media/error.png";
        death("User ".$username." has strange default page: ".$page);
    }


}

function getSecurityLevel($username) {
    $sql = "SELECT `security` FROM `usersecuritytbl` WHERE `fkUsername` = '".$username."' LIMIT 1;";
    $result = mysql_query($sql);
    if (!$result) {
        mysql_death1($sql);
        return "ERROR";
    }

    $row = mysql_fetch_array($result);
    $sec = $row['security'];
    if ($sec == "NORMAL") {
        return "Normal Security";
    } else if ($sec == "SECURE") {
        return "High Security";
    } else if ($sec == "SUPER") {
        return "Super Security";
    } else {
        return "ERROR";
        death("User ".$username." has strange security level: ".$sec);
    }


}

function getSecurityImage($username) {
    $sql = "SELECT `security` FROM `usersecuritytbl` WHERE `fkUsername` = '".$username."' LIMIT 1;";
    $result = mysql_query($sql);
    if (!$result) {
        mysql_death1($sql);
        return "ERROR";
    }

    $row = mysql_fetch_array($result);
    $sec = $row['security'];
    if ($sec == "NORMAL") {
        return "/registration/normal.png";
    } else if ($sec == "SECURE") {
        return "/registration/secure.png";
    } else if ($sec == "SUPER") {
        return "/registration/supersecure.png";
    } else {
        return "/media/error.png";
        death("User ".$username." has strange security level: ".$sec);
    }


}

function accountHasProblems($username) {
    if (hasNoEmail($username)) {
        return true;
    }
    if (hasNoSecurity($username)) {
        return true;
    }
    return false;
}

function hasNoSecurity($username) {
    $link = connect_to_boothsite();
    $sql = "SELECT true FROM `usersecuritytbl`
			WHERE `fkUsername` = '".$username."'
			LIMIT 1;";
    $result = mysql_query($sql);
    if (!$result) {
        mysql_death1($sql);
    }
    return (mysql_num_rows($result) == 0);
}

function hasNoEmail($username) {
    if (!isset($link)) $link = connect_to_boothsite();
    $sql = "SELECT true FROM `emailtbl`
			WHERE `fkUsername` = '".$username."'
			AND `email` = ''
			LIMIT 1;";
    $result = mysql_query($sql);
    if (!$result) {
        mysql_death1($sql);
    }
    return (mysql_num_rows($result) == 1);
}

function getEmail($username) {
    if (!isset($link)) $link = connect_to_boothsite();
    $sql = "SELECT `email` FROM `emailtbl` WHERE `fkUsername` = '".$username."' LIMIT 1;";
    $result = mysql_query($sql);
    if (!$result) {
        mysql_death1($sql);
        return "ERROR";
    }

    $row = mysql_fetch_array($result);
    return $row['email'];

}

function getEmailFooter() {
    $now = date("Y-m-d H:i:s");
    return "<div style = \"font-size: 8px\">Sent automatically from Boothi.ca - $now</div>";
}

function isModerator($username) {

    if ($username == "" || $username == null) {
        death("Attempted to check moderator status when user not logged in IP:".get_ip_address());
        return false;
    }
    if (!isset($dblink)) $dblink = connect_boothDB();
    $sql = "SELECT `isAdmin` FROM `logintbl` WHERE `username` = '".$username."' LIMIT 2";
    $result = $dblink->query($sql);
    if ($result) {
        $num = $result->num_rows;
        if ($num == 1) {
            $row = $result->fetch_array();
            $isAdmin = ($row['isAdmin'] == 1);
            if ($isAdmin) {
                return true;
            } else {
                return false;
            }

        } else if ($num == 0) {
            death("SEVERE: Logged in as non-existent user!!! (".$username.")\n\nIP:".get_ip_address());
            $_SESSION['username'] = null;
            return false;
        } else {
            death("Multiple entries in logintbl.  Name: ".$username.", IP:".get_ip_address());
            return false;
        }
    } else {
        sql_death1($sql);
        return false;
    }

}

function isDeveloper($username) {
    return false;
//    if ($username == "" || $username == null) {
//        death("Attempted to check developer status when user not logged in IP:".get_ip_address());
//        return false;
//    }
//    //TODO Check hash -BJ
//    if (!isset($link)) $link = connect_to_boothsite();
//    $sql = "SELECT true FROM `usersdevstbl` WHERE `fkUsername` = '".$username."' LIMIT 2";
//    $result = mysql_query($sql);
//    if ($result) {
//        $num = mysql_num_rows($result);
//        if ($num == 1) {
//            return true;
//        } else if ($num == 0) {
//            return false;
//        } else {
//            death("Multiple entries in logintbl.  Name: ".$username.", IP:".get_ip_address());
//            return false;
//        }
//    } else {
//        mysql_death1($sql);
//        return false;
//    }
}

function create_generic_header($string) {

    echo "
						<div class = 'contentheaderbarregion'>
							<div class = 'contentheaderbar'>
								<div class = 'friendstatus'>
									<div style = 'position: absolute; top: 5px;'>
										".$string."
									</div>
								</div>
							</div>
						</div>
	";

}

function parameterIsMissingAndEchoFailureMessage($param) {

    if (!isset($_POST[$param]) || strlen($_POST[$param]) == 0) {
        $arr = array("error" => "Missing or empty POST parameter: ".$param);
        echo json_encode($arr);
        return true;
    }
    return false;
}

function sendBoothicaEmail($emailAddress, $subject, $message) {
    //TODO: Re-enable emails before launch
    return;
//    $headers = "From: Boothi.ca<no-reply>\r\n";
//    $headers .= "MIME-Version: 1.0\r\n";
//    $headers .= "Content-type: text/html; charset=utf-8\r\n";
//    mail($emailAddress, $subject, $message, $headers);
}
