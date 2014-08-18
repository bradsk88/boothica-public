<?PHP		
	require_once("{$_SERVER['DOCUMENT_ROOT']}/common/user_utils.php");
	//TODO: don't show camera buttons if the camera fails to open -BJ
	require_once("{$_SERVER['DOCUMENT_ROOT']}/newbooth/mobile_utils.php");
	redirectIfMobile();
	
	include("{$_SERVER['DOCUMENT_ROOT']}/content/html.php");
	echo "
		<link rel = 'stylesheet' href = '/css/capture.css'  type='text/css' media='screen' />
	";
	include("{$_SERVER['DOCUMENT_ROOT']}/content/top.php");
	
	if (!isset($_SESSION['username'])) {
		go_to_login();
        include("{$_SERVER['DOCUMENT_ROOT']}/content/bottom.php");
        return;
	}

	$username = $_SESSION['username'];
	doBanSuspendCheck($username);

	$sql = "SELECT 
		`password` 
		FROM `logintbl` 
		WHERE `username` = '".$username."' 
		LIMIT 1;";
	$result = mysql_query($sql);
	
	if (!$result) {
		go_to_db_error($sql);
	}
	$row = mysql_fetch_array($result);
	$_SESSION['issuer'] = $row['password'];
		
	removeSiteMsg("capture");

	$sql = "SELECT 
			`password` 
			FROM `logintbl` 
			WHERE `username` = '".$username."' 
			LIMIT 1;";
	$row = mysql_fetch_array(mysql_query($sql));
	
	$_SESSION['issuer'] = $row['password'];

    if (isDeveloper($username)) {
        echo "
			<script language=\"JavaScript\" src=\"/webcam/webcam_new.js\"></script>
	    ";
    } else {
        echo "
			<script language=\"JavaScript\" src=\"/webcam/webcam.js\"></script>
	    ";
    }

    $dn = getDisplayName($username);
	printUserNavBar("capture", $dn);

	$protocol = strpos($_SERVER['SERVER_SIGNATURE'], '443') !== false ? 'https://' : 'http://';
	$url = $protocol . $_SERVER['HTTP_HOST'];
	echo "
						<script type='text/javascript' src='{$url}/common/cookies.js'>
						</script>
	";
		
	echo "
				
				<center>
				<a href = '/legacy/capturebeta'>
					<div class = 'subheader' style = 'position: relative; width: 640px;' >
						If your browser doasn't load this page correctly.  Click here.
					</div>
				</a>
				<div class = 'camerasection' style = 'position: relative;'>	
					<div class = \"camera\" style = \"height: 0px; background: cyan;\" id = \"preview\">
					</div>
					<div class = 'camera' id=\"webcam\">
						<embed id = 'flash' src = \"/webcam/webcam.swf\" width = 640 height = 480 />
					</div>
					<div id = 'countDown' class = 'countdown'>
					</div>
				</div>
				<div style = \"width: 80%;\">
					<form id = \"boothform\" action = \"/newbooth/file_upload\" method = \"post\">
						<div id = 'cam_buttons'>
							<button id = \"leftbtn\" class = medbutton type=button>3, 2, 1 ...</button>
							<button id = \"rightbtn\" class = medbutton type=button>Snap!</button>
						</div>
	 
						<div style='width: 0px; height: 0px; visibility: hidden;'>
							<textarea id=\"image\"  name = \"image\" ></textarea>
						</div>
					 
						<textarea id=\"blurb\" name = \"blurb\" style='width: 640px; height: 200px; resize: vertical;' ></textarea><br/>
					</form>
					<div id = 'status' style = 'height: 32px;'></div>
				</div>
				</center>
	"; 
	include("{$_SERVER['DOCUMENT_ROOT']}/content/bottom.php"); 
