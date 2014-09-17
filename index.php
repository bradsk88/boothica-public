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
			echo "
            <head><meta name=\"keywords\" content=\"dailybooth, social, photography, photo, socialnetworking, microblogging, community, web2.0, pictures, blog, photos\">
			<noscript>
						Your browser either doesn't support Javascript or it has been disabled.  This site absolutely requires JS.<br/>  
						<a href = \"http://enable-javascript.com/\">click here to see how to re-enable Javascript</a>
			</noscript>
			<script type = 'text/javascript'>location.href = '/info/news';</script>
			</head>";
		}
	
		include "{$_SERVER['DOCUMENT_ROOT']}/content/html.php";
		include "{$_SERVER['DOCUMENT_ROOT']}/content/top.php";
		echo "Logging you in...";
		include "{$_SERVER['DOCUMENT_ROOT']}/content/bottom.php";
		
	}