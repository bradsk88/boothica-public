<?PHP

require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";
require_common("upload_utils");
require_common("utils");
require_common("internal_utils");
require_common("db");
require_lib("h2o-php/h2o");

define('EMPTYBOOTHER', -1);
define('NONEXISTENT_BOOTHER', -2);
define('SESSION_NOT_SET', -3);
define('DATABASE_ERROR', -4);
define('EMPTYUSER', -5);

function isActive($username) {

    $dblink = connect_boothDB();
    $sql = "SELECT COUNT(*) as `num` FROM `boothnumbers` WHERE `fkUsername` = '".$username."';";
    $result = $dblink->query($sql);
    if (!$result) {
        sql_death1($sql);
        return false;
    }
    $row = $result->fetch_array();
    if ($row['num'] > 10) {
        return true;
    }
    return false;

}

function upload_comment($isphoto, $comment, $boothnumber, $boother, $ext) {
    $hasphoto = 0;
    if ($isphoto) {
        $hasphoto = 1;
    }


    if (strlen($boother) == 0) {
        return array("error" => "Empty boother name");
    }

    if (!userExists($boother)) {
        return array("error" => "User does not exist: ".$boother);
    }

    if (!isLoggedIn()) {
        return array("error" => "Must be logged in");
    }

    $username = $_SESSION['username'];

    $dblink = connect_boothDB();
    $sqlBuilder = new h2o("{$_SERVER['DOCUMENT_ROOT']}/actions/comment/queries/insertComment.mst.sql");
    $sql = $sqlBuilder->render(array(
        "boothNumber" => $dblink->real_escape_string($boothnumber),
        "username" => $dblink->real_escape_string($username),
        "fileExtension" => $dblink->real_escape_string($ext),
        "comment" => $dblink->real_escape_string(formatComment($comment, $username))
    ));
    $result2 = $dblink->query($sql);
    if (!$result2) {
        return array(
            "error" => sql_death1($sql)
        );
    }

    $sqlBuilder = new h2o("{$_SERVER['DOCUMENT_ROOT']}/actions/comment/queries/getCommentNumber.mst.sql");
    $sql = $sqlBuilder->render(array(
        "username" => $username
    ));
    $result3 = $dblink->query($sql);
    if (!$result3) {
        return array(
            "error" => sql_death1($sql)
        );
    }

    $row2 = $result3->fetch_array();
    $commentnumber = $row2['pkCommentNumber'];

    handleMentions($commentnumber, $comment, $boothnumber, $boother, $username);

    if ($boother != $username && !isIgnoring($boother, $username)) {
        addMention($boothnumber, $boother, $username, $commentnumber, $sql);
        sendEmails($boothnumber, $boother, $username);
    }

    $sqlBuilder = new h2o("{$_SERVER['DOCUMENT_ROOT']}/actions/comment/queries/updateImageName.mst.sql");
    $sql = $sqlBuilder->render(array(
        "imageTitle" => generateUserUniqueHash($username),
        "hasMedia" => $hasphoto,
        "commentNumber" => $commentnumber
    ));
    $result4 = $dblink->query($sql);

    if (!$result4) {
        return array(
            "error" => sql_death1($sql)
        );
    }

    $sqlBuilder = new h2o("{$_SERVER['DOCUMENT_ROOT']}/actions/comment/queries/updateCommentRange.mst.sql");
    $sql = $sqlBuilder->render(array(
        "boothNumber" => $boothnumber,
        "commentNumber" => $commentnumber
    ));
    $updateres = $dblink->query($sql);
    if (!$updateres) {
        return array(
            "error" => sql_death1($sql)
        );
    }

    $activitytype = "comment";
    addActivity($username, $row2['pkCommentNumber'], $activitytype);

    return array("success" => array("message" => "Comment was uploaded successfully"));
}

function handleMentions($commentnumber, $comment, $boothnumber, $boother, $username) {
    $dblink = connect_boothDB();
    preg_match_all("/@([a-zA-Z0-9_-]+)/", $comment, $mentions, PREG_PATTERN_ORDER);
    foreach ($mentions[1] as $mention) {

        if (strtolower($mention) != $username) {
            if (strtolower($mention) == "everyone") {
                if (isActive($username)) {
                    $sql = "SELECT DISTINCT fkUsername FROM commentstbl WHERE fkNumber = " . $boothnumber . ";";
                    $posters = $dblink->query($sql);
                    if (!$posters) {
                        sql_death1($sql);
                    }
                    mentionAllCommenters($boothnumber, $posters, $username, $commentnumber);
                    $mentionboothres = mentionBoothPoster($boothnumber, $boother, $username, $commentnumber, 'BOOTH');
                    if (!$mentionboothres) {
                        break;
                    }
                } else {
                    $okreturn = 1;
                }
                break;
            } else {
                $mspr = mentionSpecificPerson($boothnumber, $boother, $username, $mention, $commentnumber, 'BOOTH');
                if (!$mspr) {
                    break;
                }
            }
        }
    }

}

/**
 * @param $boothnumber
 * @param $username
 * @param $mention
 * @param $commentnumber
 */
function mentionSpecificPerson($boothnumber, $boother, $username, $mention, $commentnumber, $location)
{

    $dblink = connect_boothDB();
    $sql = "SELECT COUNT(*) as `num` FROM `mentionstbl` WHERE
                `fkMentionerName` = '".$username."' AND
                `fkMentionedName` = '".strtolower($mention)."' AND
                `fkIndex` = ".$commentnumber." AND
                `fkBoothNumber` = '".$boothnumber."' AND
                `location` = '".$location."';";
    $countQuery = $dblink->query($sql);

    if (!$countQuery) {
        sql_death1($sql);
        return false;
    }
    $count = sql_get_expectOneRow($countQuery, "num");

    if ($count == 0 ) {

        $sql2 = "SELECT COUNT(*) as `num` FROM `emailtbl` WHERE
                `fkUsername` = '".$username."' AND
                `mention` = 1;";
        $emailQuery = $dblink->query($sql2);
        $acceptsMentions = sql_get_expectOneRow($emailQuery, "num");

        if ($acceptsMentions == 1) {
            $email = getEmail(strtolower($mention));
            $name = $_SESSION['username'];
            $subject = "Mention: $name";
            $site = $_SERVER['HTTP_HOST'];
            sendBoothicaEmail($email, $subject, makeMentionMessage($name, $site, $boothnumber, $boother));
        }

    }

    $putmention = "REPLACE INTO
								`mentionstbl`
								(`fkMentionerName`,
								`fkMentionedName`,
								`fkIndex`,
								`fkBoothNumber`,`location`)
								VALUES
								('" . $username . "', '" . strtolower($mention) . "', " . $commentnumber . ", " . $boothnumber . ", (SELECT `val` FROM `locationstbl` WHERE `location` = '".$location."'));";

    $mentionresult = $dblink->query($putmention);
    if (!$mentionresult) {
        sql_death1($putmention);
        return false;
    }
    return true;
}

/**
 * @param $boothnumber
 * @param $boother
 * @param $username
 * @param $commentnumber
 */
function mentionBoothPoster($boothnumber, $boother, $username, $commentnumber, $location)
{
    $dblink = connect_boothDB();
    $mentionboother = "REPLACE INTO
								`mentionstbl`
								(`fkMentionerName`,
								`fkMentionedName`,
								`fkIndex`,
								`fkBoothNumber`,`location`)
								VALUES
								('" . $username . "', '" . $boother . "', " . $commentnumber . ", " . $boothnumber . ", (SELECT `val` FROM `locationstbl` WHERE `location` = '".$location."' LIMIT 1));";
    $mentionbootherresult = $dblink->query($mentionboother);
    if (!$mentionbootherresult) {
        sql_death1($mentionboother);
        return false;
    }
    return true;
}

/**
 * @param $boothnumber
 * @param $posters
 * @param $username
 * @param $commentnumber
 */
function mentionAllCommenters($boothnumber, $posters, $username, $commentnumber)
{
    $dblink = connect_boothDB();
    while ($row = $posters->fetch_array()) {
        if ($row['fkUsername'] == $username) {
            continue;
        }
        $mentionall = "REPLACE INTO
								`mentionstbl`
								(`fkMentionerName`,
								`fkMentionedName`,
								`fkIndex`,
								`fkBoothNumber`)
								VALUES
								('" . $username . "', '" . $row['fkUsername'] . "', " . $commentnumber . ", " . $boothnumber . ");";
        $mentionallresult = $dblink->query($mentionall);
        if (!$mentionallresult) {
            sql_death1($mentionall);
            break;
        }
    }
}

function formatComment($comment, $username)
{
    if (!isModerator($username)) {
        $comment = str_replace("<", "&lt;", $comment);
        $comment = str_replace(">", "&gt;", $comment);
    }
    $formattedcomment = handle_links($comment);
    $formattedcomment = handle_mentions($formattedcomment);
    $formattedcomment = handle_hashtags($formattedcomment);
    $formattedcomment = preg_replace('/(\r\n|\n|\r)/', '<br/>', $formattedcomment);
    return $formattedcomment;
}

function sendEmails($boothnumber, $boother, $username )
{
    $site = base();
    $email = getEmail($boother);
    $displayname = getDisplayName($username);

    $msg = "$displayname commented on <a href= '$site/users/$boother/$boothnumber'>Your booth</a>.";
    sendBoothicaEmail($email, "Booth # $boothnumber New comment!", $msg);
}

/**
 * @param $boothnumber
 * @param $boother
 * @param $username
 * @param $commentnumber
 * @param $sql
 */
function addMention($boothnumber, $boother, $username, $commentnumber, $sql)
{
    $dblink = connect_boothDB();
    $putmention = "REPLACE INTO
						`mentionstbl`
						(`fkMentionerName`,
						`fkMentionedName`,
						`fkIndex`,
						`fkBoothNumber`)
						VALUES
						('" . $username . "', '" . $boother . "', " . $commentnumber . ", " . $boothnumber . ");";
    $mentionboother = $dblink->query($putmention);

    if (!$mentionboother) {
        sql_death1($sql);
    }
}

/**
 * @param $username The name of the user performing the activity
 * @param $commentno the comment number
 * @param $activitytype the type of activity
 */
function addActivity($username, $commentno, $activitytype)
{
    $dblink = connect_boothDB();
    $sql = "INSERT INTO
		`activitytbl`
		(`fkUsername`,
		`fkIndex`,
		`type`)
		VALUES
		('".$username."',
		".$commentno.",
		'".$activitytype."');";
    $result5 = $dblink->query($sql);
    if (!$result5) {
        sql_death1($sql);
    }
}

function makeMentionMessage($name, $site, $number, $boother) {
    $randomDiv = generateRandomString();
    $msg = "$name mentioned you on $boother's booth<br/>
				<br/>
				<a href = \"http://$site/users/$boother/$number\">
					<div id = \"$randomDiv\" style = \"background: #6bdbb3; box-sizing:border-box; -moz-box-sizing:border-box; -webkit-box-sizing:border-box; border: 1px black solid; cursor: pointer; color: black; padding-top: 10px;  padding-bottom: 10px; text-decoration:none; max-width: 400px;\">
						<center>Click To View</center>
					</div>
				</a>";
    $msg .= getEmailFooter();
    return $msg;

}
