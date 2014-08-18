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
			<noscript>
						Your browser either doesn't support Javascript or it has been disabled.  This site absolutely requires JS.<br/>  
						<a href = \"http://enable-javascript.com/\">click here to see how to re-enable Javascript</a>
			</noscript>
			<script type = 'text/javascript'>location.href = '/activity';</script>";
	
	} else {
		
		if (!isset($_COOKIE['userid'])) {
			echo "
			<noscript>
						Your browser either doesn't support Javascript or it has been disabled.  This site absolutely requires JS.<br/>  
						<a href = \"http://enable-javascript.com/\">click here to see how to re-enable Javascript</a>
			</noscript>
			<script type = 'text/javascript'>location.href = '/info/news';</script>";
		}
	
		include "{$_SERVER['DOCUMENT_ROOT']}/content/html.php";
		include "{$_SERVER['DOCUMENT_ROOT']}/content/top.php";
		echo "Logging you in...";
		include "{$_SERVER['DOCUMENT_ROOT']}/content/bottom.php";
		
	}