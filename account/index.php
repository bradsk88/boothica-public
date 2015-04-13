<?PHP

require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";
require_once("{$_SERVER['DOCUMENT_ROOT']}/content/ContentPage.php");
require_common("utils");

connect_to_boothsite();

//TODO: ADD TIME ZONES
//require_asset("TimeZoneSelector");

error_reporting(0);
session_start();
main();

function main() {
    if (!isset($_SESSION['username'])) {
        go_to_login();
        return;
    }

    $username = $_SESSION['username'];

    $body = "";

    if (isPublic($username)) {
        $body .= printPublicBanner();
    } else {
        $body .= printPrivateBanner();
    }

    if (isset($_POST['topmessage'])) {
        if (isset($_POST['vital'])) {
            echo "<div class = \"account-problem\">";
        } else {
            echo "<div class = \"account-ok\">";
        }
        echo urldecode($_POST['topmessage']).
        "</div>
        ";
    }

    $page = new ContentPage("void");
    $body .= printSettings($username);
    $page->meta("<link rel='stylesheet' href='/css/account.css' type='text/css' media='screen' />");
    $page->meta("<link rel='stylesheet' href='/css/activity.css' type='text/css' media='screen' />");
    $page->body($body);
    $page->echoPage();

}

function printPublicBanner() {
    return "
            <div class = 'privacy-off'>
				<div style = \"float: left; width: 75%;\">
					Your account is currently PUBLIC.  Anyone may view your booths and comments.<br/>
				</div>
				<div style = \"float: left; width: 25%; text-align: right\">
					<a href = \"/account/setpublic?private=false\">Go Private</a>
				</div>
				<div style = \"clear: both;\"></div>
			</div>
	";
}

function printPrivateBanner() {
    return "
            <div class = 'privacy-on'>
				<div style = \"float: left; width: 75%;\">
					Your account is currently PRIVATE.  Only your friends may view your booths and comments.<br/>
				</div>
				<div style = \"float: left; width: 25%; text-align: right\">
					<a href = \"/account/setpublic?private=true\">Go Public</a>
				</div>
				<div style = \"clear: both;\"></div>
			</div>
	";
}

function printSettings($username) {
    
    return "
            <div class = 'setting'>
				<stitle>About Me</stitle><br/>
				<div class = \"setting-desc\">
					Your \"About Me\" is a page where you can write a short bio.  It also displays your public image.<br/>
				</div>
				<div class = \"setting-cur\">
					<a class = \"setting-link\" href = \"/userpages/about\">View your About Me page</a>
					<a class = \"setting-link\" href = \"/account/aboutme\"><img src= \"/media/edit.png\"> Edit</a>
					<div style = \"clear: both;\"></div>
				</div>
			</div>
			
			<div class = 'setting-with-image'>
				<stitle>Public Image</stitle><br/>
				<div class = \"setting-img-right\">
					<div class = \"setting-img-desc\">
						This is the image that will be shown on your \"About Me\" page.  Also, if your account is private users you are not friends with will see this image beside your posts, instead of the image from your latest booth.
					</div>
				</div>
				<div class = \"setting-img-left\">
					<div style = \"background-image: url(".getPublicImage($username)."); background-size: cover; background-position: center; width: 100px; height: 100px;\" ></div>
				</div>
				<div class = \"setting-cur\">
                    <a class = \"setting-link\" href = \"/account/publiciconcapture\"><img src= \"/media/edit.png\"> Edit</a><br/>
                    <a class = \"setting-link\" href = \"/actions/publiciconremove\"><img src= \"/media/delete.png\"> Remove</a>
					<div style = \"clear: both;\"></div>
                </div>
				<div style = \"clear: both\"></div>
			</div>
		
			<div class = 'setting-with-image'>
				<stitle>Security</stitle><br/>
				<div class = \"setting-img-right\">
				    <div class = \"setting-img-desc\">
					    Your security setting determines how easy it is to regain access to your account when you have lost your password.<br/><br/>
					    Choosing a high security setting will make it harder for someone else to get into your account but is only recommended if you are confident you will not forget your password.
				    </div>
				</div>
				<div class = \"setting-img-left\">
					<div style = \"background-image: url(".getSecurityImage($username)."); background-size: cover; background-position: center; width: 100px; height: 100px;\" ></div>
				</div>
				<div style = \"clear: both\"></div>
				<div class = \"setting-cur\">
					Current setting:
            ".
            getSecurityLevel($username)
            ."
					<a href = \"/account/changesecurity\"><img src= \"/media/edit.png\"> Edit</a>
					<div style = \"clear: both;\"></div>
				</div>
			</div>

			<!--<div class = 'setting-with-image'>
				<stitle>Default Feed Page</stitle><br/>
				<div class = \"setting-img-right\">
				    <div class = \"setting-desc\">
					    This setting determines which page will be shown when you click on the \"Boothi.ca\" button at the top of every page.
				    </div>
				</div>
				<div class = \"setting-img-left\">
					<div style = \"background-image: url(".getLayoutImage($username)."); background-size: cover; background-position: center; width: 100px; height: 100px;\" ></div>
				</div>
				<div style = \"clear: both\"></div>
				<div class = \"setting-cur\">
					Current setting:
            ".
            getDefaultLayout($username)
            ."
                    <a href = \"/account/changelayout\"><img src= \"/media/edit.png\"> Edit</a>
                </div>
            </div>-->
		
			<div class = 'setting'>
				<stitle>Password</stitle><br/>
				<div class = \"setting-desc\">
					Used for accessing your account on the website or mobile apps.<br/>
					<b>Boothi.ca will NEVER ask you to tell us your password.</b>
				</div>
				<div class = \"setting-cur\">
					<a class = \"setting-link\" href = \"/account/changepassword\"><img src= \"/media/edit.png\"> Change Password</a>
					<div style = \"clear: both;\"></div>
				</div>
			</div>
			
			<div class = 'setting'>
				<stitle>Email</stitle><br/>
				<div class = \"setting-desc\">
					The email address at which you will recieve notifications.  You can also regain access to your account using this email address if you have forgotten your password.
				</div>
				<div class = \"setting-cur\">
					".getEmail($username)."
					<a class = \"setting-link\" href = \"/account/changeemail\"><img src= \"/media/edit.png\"> Edit</a>
				</div>
					<div style = \"clear: both;\"></div>
			</div>
			
			<div class = 'setting'>
				<stitle>Email Preferences</stitle><br/>
				<div class = \"setting-desc\">
					Choose which notifications will be sent to your email address when activity occurs at Boothi.ca.
				</div>
				<div class = \"setting-cur\">
					<a class = \"setting-link\" href = \"/account/changeemailprefs\"><img src= \"/media/edit.png\"> Edit</a>
					<div style = \"clear: both;\"></div>
				</div>
			</div>
    ";
}