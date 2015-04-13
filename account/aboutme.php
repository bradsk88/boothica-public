<?PHP

if (!isset($_GET['about'])) {

	include "{$_SERVER['DOCUMENT_ROOT']}/common/html.php";
	echo "
		<link rel='stylesheet' href='/css/tutorial.css' type='text/css' media='screen' />
		<link rel='stylesheet' href='/css/smallpage.css' type='text/css' media='screen' />
	";
	include "{$_SERVER['DOCUMENT_ROOT']}/content/top.php";
	echo "
			<div style = 'width: 600px; margin-left: auto; margin-right: auto;'>
				<div class = 'row'>
					<h3>Tell us about yourself!</h3><br/>
				</div>
				<div class = 'row'>
					Write about your interests.  Write about your character.  Or even post some links to your other internet content.  The choice is up to you!
				</div>
				<div class = 'row'></div>
				<form action = '/account/aboutme' action = 'get'>
					<div>
						<div class = 'infobox'' >
							<div style = 'padding: 10px;'>
								<textarea style = 'width: 100%; height: 396px; resize: none; padding: 0px; border: none;' name = 'about'>";
	$boothsql = "SELECT 
				`about`
				FROM `usersabouttbl` 
				WHERE `fkUsername`='".$_SESSION['username']."'
				LIMIT 1;";
	$boothquery = mysql_query($boothsql);
	if (!$boothquery) {
		echo mysql_death1($boothsql);
	} else {
		$row = mysql_fetch_array($boothquery);
		echo $row['about'];
	}
	echo "</textarea>
							</div>
						</div>
					</div>
					<div align = center class = 'continue' style = 'float: left; width: 100%' onclick='javascript:document.forms[0].submit();'>
						<h3>Save</h3>
					</div>
					<div style = 'clear: both;'></div>
					<input type = 'hidden' name = 'step' value = '3' />
				</form>
			</div>
		</div>
	</div>
	";
} else {
	include "{$_SERVER['DOCUMENT_ROOT']}/common/html.php";
	echo "
		<link rel='stylesheet' href='/css/tutorial.css' type='text/css' media='screen' />
	";
	include "{$_SERVER['DOCUMENT_ROOT']}/common/smallpage_top.php";
	include("{$_SERVER['DOCUMENT_ROOT']}/common/header.php");
	$sql = "REPLACE INTO `usersabouttbl` (`fkUsername`, `about`) VALUES ('".$username."', '".mysql_real_escape_string($_GET['about'])."');";
	$aboutres = mysql_query($sql);
	if (!$aboutres) {
		mysql_death1($sql);
		echo "
			<div class = 'smallcenteredcontent' align = center>
				<div class = 'smallcontentarea'>
					<div class = 'row'>
						<p>
						There was a problem setting your \"About Me\".  You may continue.  But you should check your account settings once you start using the site.
					</div>
				</div>
			</div>
		";
	} else {
		echo "
			<div class = 'smallcenteredcontent' align = center>
				<div class = 'smallcontentarea'>
					<div class = 'row'>
						<h1>\"About Me\" updated successfully</h1>
						<p>
						<a href = \"/account\">
							<span class = 'navbutton'>
								Account Settings
							</span>
						</a>
						<a href = \"/users/".$_SESSION['username']."/about\">
							<span class = 'navbutton'>
								View \"About Me\" page
							</span>
						</a>
						<a href = \"/account/publiciconcapture\">
							<span class = 'navbuttonend'>
								Snap \"About Me\" pic
							</span>
						</a>
					</div>
				</div>
			</div>
		";
	}
}