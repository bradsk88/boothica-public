<?PHP 
	include("{$_SERVER['DOCUMENT_ROOT']}/content/html.php");
	include("{$_SERVER['DOCUMENT_ROOT']}/content/top.php");

	if (isset($_SESSION['username'])) {
		echo "
							<div class = 'contentheaderbarregion'>
								<div class = 'contentheaderbar'>
									<div class = 'friendstatus'>
										<div style = 'position: absolute; top: 5px'>
											Log in to account settings
										</div>
									</div>
								</div>";
		
			//put controls here
		
		echo "
							</div>
		";
		
		echo "
							<h1>Log in to change your account settings</h1>
							<form action = '/account' method=post>
								password: <input type = 'password' name = 'password'/>
								<input type = 'submit' value = 'Log in'>
							</form>
		";
		
		if (isset($_GET['badlogin'])) {
		
			echo "<font color=red><i>The password you provided was incorrect.
			<br/>Try again.</i></font>";
		
		}
	} else {
		go_to_login();
	}
		
	include("{$_SERVER['DOCUMENT_ROOT']}/content/bottom.php");
?>