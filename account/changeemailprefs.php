<?php

require_once "{$_SERVER['DOCUMENT_ROOT']}/common/utils.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/common/db.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/content/ContentPage.php";

session_start();
connect_to_boothsite();
main();

function main() {

    if (!isset($_SESSION['username'])) {
        go_to_login();
        return;
    }

    $username = $_SESSION['username'];

    $sql = "SELECT
					*
					FROM `emailtbl`
					WHERE `fkUsername` = '".$username."' LIMIT 1;";
    $result = sql_query($sql);
    $assoc = $result->fetch_assoc();

    $mail = $assoc["email"];
//email settings

    if (isset($mail) && $mail != "") {

        $body = "
    <form id = \"emailform\" action = \"/actions/changeemailprefs\" method = \"post\">
    <div class = 'setting'>
        <stitle>Email Notifications </stitle>(put a check beside the emails you would like to receive)<br/>
        <div class = \"setting-desc\">
        ";

        $announcements = $assoc["announcements"];
        if ($announcements == 1) {
            $body .= "
										<input type = 'checkbox' id = 'announcements' name = 'announcements' value = 1 checked/> Site Announcements (extremely rare)<br/>
					";
        } else {
            $body .= "
										<input type = 'checkbox' id = 'announcements' name = 'announcements' value = 1 /> Site Announcements (extremely rare)<br/>
					";
        }

        $fromMods = $assoc["fromMods"];
        if ($fromMods == 1) {

            $body .= "
										<input type = 'checkbox' id = 'fromMods' name = 'fromMods' value = 1 checked/> From moderators<br/>
					";
        } else {
            $body .= "
										<input type = 'checkbox' id = 'fromMods' name = 'fromMods' value = 1 /> From moderators<br/>
					";
        }
        $newPM = $assoc["newPM"];
        if ($newPM == 1) {

            $body .= "
										<input type = 'checkbox' id = 'newPM' name = 'newPM' value = 1 checked/> When you receive a new Private Message<br/>
					";
        } else {
            $body .= "
										<input type = 'checkbox' id = 'newPM' name = 'newPM' value = 1 /> When you receive a new Private Message<br/>
					";
        }
        $friendBooth = $assoc["friendBooth"];
        if ($friendBooth == 1) {
            $body .= "
										<input type = 'checkbox' id = 'friendBooth' name = 'friendBooth' value = 1 checked/> When your friends post new booths<br/>
					";
        } else {
            $body .= "
										<input type = 'checkbox' id = 'friendBooth' name = 'friendBooth' value = 1 /> When your friends post new booths<br/>
					";
        }
        $boothComment = $assoc["boothComment"];
        if ($boothComment == 1) {
            $body .= "
										<input type = 'checkbox' id = 'boothComment' name = 'boothComment' value = 1 checked/> When someone comments on one of your booths<br/>
					";
        } else {
            $body .= "
										<input type = 'checkbox' id = 'boothComment' name = 'boothComment' value = 1 /> When someone comments on one of your booths<br/>
					";
        }
        $mention = $assoc["mention"];
        if ($mention == 1) {
            $body .= "
                                        <input type = 'checkbox' id = 'mention' name = 'mention' value = 1 checked/> When someone mentions you anywhere on the site<br/>
         ";
        } else {
            $body .= "
										<input type = 'checkbox' id = 'mention' name = 'mention' value = 1/>When someone mentions you anywhere on the site<br/>
					";
        }
        $friendRequest = $assoc["friendRequest"];
        if ($friendRequest == 1) {
            $body .= "
										<input type = 'checkbox' id = 'friendRequest' name = 'friendRequest' value = 1 checked/> When someone asks to be your friend<br/>
					";
        } else {
            $body .= "
										<input type = 'checkbox' id = 'friendRequest' name = 'friendRequest' value = 1 /> When someone asks to be your friend<br/>
					";
        }
        $friendAccept = $assoc["friendAccept"];
        if ($friendAccept == 1) {
            $body .= "
										<input type = 'checkbox' id = 'friendAccept' name = 'friendAccept' value = 1 checked/> When someone accepts your friend request<br/>
					";
        } else {
            $body .= "
										<input type = 'checkbox' id = 'friendAccept' name = 'friendAccept' value = 1 /> When someone accepts your friend request<br/>
					";
        }

        $body .= "
                </div>
            <div class = \"setting-cur\">
                <span id = \"submit\">
                    <img src= \"/media/edit.png\"> Submit Changes
                </span>
            </div>
        </div>
        </form>
        <script type = \"text/javascript\">
            $('#submit').one('click', function() {
                $('#emailform').submit();
            });
        </script>";

    } else if (isset($mail) && $mail == "") {
        $body = "<i>You must first <a href = \"/account/changeemail\" >specify an email address</a></i>";
    } else {

        $body = "<i>Failed to load.  Please contact a moderator.</i>";

    }

    $page = new ContentPage("void");
    $page->meta("<link rel = 'stylesheet' href = '/css/account.css'  type='text/css' media='screen' />");
    $page->meta("<link rel = 'stylesheet' href = '/css/activity.css'  type='text/css' media='screen' />");
    $page->meta("<script type = \"text/javascript\" src = \"/booth/booth-scripts.js.php\"></script>");
    $page->body($body);
    $page->echoPage();

    //end email settings


}

