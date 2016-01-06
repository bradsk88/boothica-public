<?PHP

    require_once "{$_SERVER['DOCUMENT_ROOT']}/common/utils.php";

	//methods for sending emails to friends after posting a booth
	function sendNewBoothEmail($username, $number) {

        $dblink = connect_boothDB();
		$sql = "SELECT
			`email`
			FROM `emailtbl`
			WHERE `fkUsername` IN
				(SELECT
				`fkUsername`
				FROM `friendstbl`
				WHERE `fkFriendName` = '".$username."')
			AND `friendBooth` = 1
			AND NOT `fkUsername` = '".$username."';";
		$emailres = $dblink->query($sql);
		if (!$emailres) {
			sql_death1($sql);
		}

		$site = $_SERVER['HTTP_HOST'];
		$headers = "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
		$headers .= "From: Boothi.ca<noreply@$site>\r\n";
		$name = $_SESSION['username'];
		require_once "{$_SERVER['DOCUMENT_ROOT']}/common/internal_utils.php";
		while ($row = $emailres->fetch_array()) {
			mail(
				$row['email'],
				"New Booth: $name",
				makeMessage($name, $site, $number, $username),
				$headers
			);
		}
	
	}
	
	function makeMessage($name, $site, $number, $username) {
		$randomDiv = generateRandomString();
		$msg = "Your friend $name has posted a new booth<br/>
				<br/>
				<a href = \"http://$site/users/$username/$number\">
					<div id = \"$randomDiv\" style = \"background: #6bdbb3; box-sizing:border-box; -moz-box-sizing:border-box; -webkit-box-sizing:border-box; border: 1px black solid; cursor: pointer; color: black; padding-top: 10px;  padding-bottom: 10px; text-decoration:none; max-width: 400px;\">
						<center>Click To View</center>
					</div>
				</a>";
        $msg .= getEmailFooter();
		return $msg;
	
	}
