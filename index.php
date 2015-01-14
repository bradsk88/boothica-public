<?PHP 
	session_start();

	if (isset($_SESSION['username'])) {
	
		$username = strtolower($_SESSION['username']);		
		$sql = "SELECT 
				`username` 
				FROM `logintbl` 
				WHERE `username` = '".$username."' 
				AND NOW() > `lastonline` + INTERVAL 6 HOUR";
	
		echo "
            <head><meta name=\"keywords\" content=\"dailybooth, social, photography, photo, socialnetworking, microblogging, community, web2.0, pictures, blog, photos\">
			<noscript>
						Your browser either doesn't support Javascript or it has been disabled.  This site absolutely requires JS.<br/>  
						<a href = \"http://enable-javascript.com/\">click here to see how to re-enable Javascript</a>
			</noscript>
			<script type = 'text/javascript'>location.href = '/activity';</script>
			</head>
			";

	} else {
		
		if (!isset($_COOKIE['userid'])) {
			displayLandingPage();
            return;
		}

        //Including the old /contnet/html file here will cause the log-in redirect to happen automatically.
		include "{$_SERVER['DOCUMENT_ROOT']}/content/html.php";
		include "{$_SERVER['DOCUMENT_ROOT']}/content/top.php";
		echo "Logging you in...";
		include "{$_SERVER['DOCUMENT_ROOT']}/content/bottom.php";
		
	}

function displayLandingPage() {

    include "{$_SERVER['DOCUMENT_ROOT']}/content/ContentPage.php";
    $page = new ContentPage('void');

    $body = <<<EOT
<div class = "welcome-section">
    <div class = "welcome-logo">
        Welcome to Boothi.ca
    </div>
    <div class = "welcome-login">
        <div class = "welcome-login-header">
            Existing users:
        </div>
        <form action = '/dologin' method=post>
            <div class = "welcome-username-label">
                Username
            </div>
            <div class = "welcome-username-field">
                <input type = 'text' name = 'username'/>
            </div>
            <div class = "welcome-username-end"></div>
            <div class = "welcome-password-label">
                Password
            </div>
            <div class = "welcome-password-field">
                <input type = 'password' name = 'password'/>
            </div>
            <div class = "welcome-login-button">
                <button type = 'submit'>Log in</button>
            </div>
        </form>
    </div>
    <div class = "welcome-brief">
        Boothi.ca is a place to share your face and your thoughts with the internet
        <div class = "welcome-signup-button">
            <a href = '/registration'>
                <button>Click here to sign up</button>
            </a>
        </div>
    </div>
</div>
EOT;

    $page->body($body);

    //TODO: Load these scripts justintime
    $page->meta("<script type = 'text/javascript' src = '/activity/activity-scripts.js'></script>");
    $page->meta("<script type = 'text/javascript' src = '/booth/booth-scripts.js.php'></script>");
    $page->meta("<script type = 'text/javascript' src = '/booth/booth-comment-scripts.js'></script>");
    $page->meta("<script type = 'text/javascript' src = '/booth/userbooths-scripts.js'></script>");
    $page->meta("<script type = 'text/javascript' src = '/common/navigation-scripts.js'></script>");
    $page->meta("<script type = 'text/javascript' src = '/messages/pm-scripts.js'></script>");
    $page->meta("<script type = 'text/javascript' src = '/common/feed-scripts.js'></script>");
    $page->meta("<script type = 'text/javascript' src = '/common/truncate.js'></script>");
    $page->meta("<script type = 'text/javascript' src = '/activity/friendfeed-scripts.js'></script>");
    $page->meta("<script type = 'text/javascript' src = '/newbooth/newbooth-scripts.js'></script>");
    $page->meta("<script type = 'text/javascript' src = '/livefeed/livefeed-scripts.js'></script>");
    $page->meta("<link rel='stylesheet' href='/css/activity.css' type='text/css' media='screen' />");
    $page->meta("<link rel='stylesheet' href='/css/booth.css' type='text/css' media='screen' />");
    $page->meta("<link rel = 'stylesheet' href = '/css/capture.css'  type='text/css' media='screen' />");
    $page->meta("<link rel='stylesheet' href='/css/commentinput.css' type='text/css' media='screen' />");
    $page->meta("<link rel='stylesheet' href='/css/welcome.css' type='text/css' media='screen' />");
    $page->meta("<script type = 'text/javascript' src = '/common/jquery.a-tools-1.5.2.min.js'></script>");
    $page->meta("<script type = 'text/javascript' src = '/common/jquery.asuggest.js'></script>");
    $page->meta("<link href='http://fonts.googleapis.com/css?family=Bitter:400,700' rel='stylesheet' type='text/css'>");

    $page->echoPage();

}