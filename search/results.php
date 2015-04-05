<?

require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";
require_common("user_utils");
require_common("activity_utils");
require_common("upload_utils");

function printMeBar() {
	$urlparts = explode( '/', $_SERVER['REQUEST_URI'] );
	$displayname = getDisplayName($_SESSION['username']);
	printUserNavBar("search", $displayname);
}

include "{$_SERVER['DOCUMENT_ROOT']}/content/html.php";
echo "
					<link rel='stylesheet' href='/css/booths.css' type='text/css' media='screen' />
					<link rel='stylesheet' href='/css/comments.css' type='text/css' media='screen' />
";
include "{$_SERVER['DOCUMENT_ROOT']}/content/top.php";

main();
include "{$_SERVER['DOCUMENT_ROOT']}/content/bottom.php";

function main() {

    if (!isset($_GET["q"]) || !isset($_GET['scope'])) {
        require_common("universal_utils");
        print404();
        return;
    }

    if (!isset($_SESSION['username'])) {
        require_common("universal_utils");
        printLogin();
        return;
    }

	if (!isset($_GET['page'])) {
		$_GET['page'] = 1;	
	}
	
	printMeBar();
	
	if ($_GET['scope'] == "user") {
		echo "
				Users with names like \"".$_GET["q"]."\"
				<hr color = '#EEEEEE'>
		";
		$num = 20;
	} else if ($_GET['scope'] == "booth") {
		echo "
				Booths containing the word \"".$_GET["q"]."\"
				<hr color = '#EEEEEE'>
		";
		$num = 10;
	} else if ($_GET['scope'] == "booth_comment") {
		echo "
				Comments containing the word \"".$_GET["q"]."\"
				<hr color = '#EEEEEE'>
		";
		$num = 10;
	}
	searchBox();
	
	echo "
					<hr color = '#EEEEEE'/>
	";
	if ($_GET['scope'] == "user") {
		$sql = "SELECT `username`, `displayname`
				FROM `logintbl`
				WHERE `username` LIKE '%".mysql_real_escape_string($_GET["q"])."%'
				ORDER BY `username` ASC
				LIMIT " . $num * ($_GET['page']-1) . ", ".$num.";";
		$result = mysql_query($sql);
				
		if(!$result) {
			echo mysql_death1($sql);
		} else {
			$oneboother = (mysql_num_rows($result) == 1);
			if (mysql_num_rows($result) == 0) {
				echo "<i>No users found...</i>";
			}
			while($row = mysql_fetch_array($result)) {
				if ($oneboother) {
					if ($row['username'] == $_GET["q"]) {
						echo "<script type = \"text/javascript\">location.href = \"/users/".$_GET['q']."/\";</script>";
						return;
					}
				}
				echo "
				<a href = '/users/".$row['username']."'>".$row['displayname']."</a><br/>";
			}
		}
	} else if ($_GET['scope'] == "booth") {
		if (is_numeric($_GET["q"])) {
			$num = mysql_real_escape_string($_GET["q"]);
			$sql = "SELECT 
						`pkNumber`, 
						`fkUsername`, 
						`blurb`, 
						`imageTitle`, 
						`filetype`, 
						`imageHeightProp`,
						`datetime`, 
						HOUR( timediff( NOW( ) , `datetime` ) ) as `hours`, 
						MINUTE( timediff( NOW( ) , `datetime` ) ) as `minutes` 
					FROM `boothnumbers` b
					WHERE
					(
						'".$_SESSION['username']."' IN (
						SELECT `fkFriendname` FROM `friendstbl` WHERE `fkUsername` = b.`fkUsername`
						) OR (
							SELECT true FROM `userspublictbl` WHERE `fkUsername` = b.`fkUsername`
						)
					)
					AND `pkNumber` = ".$num."
					LIMIT 1;
			";
			$result = mysql_query($sql);
			if(!$result) {
				echo mysql_death1($sql);
			} else {
				while($row = mysql_fetch_array($result)) {
					echo "Booth number ".$num."<br/><br/>";
					$index = $row['pkNumber'];
					$activityuser = $row['fkUsername'];
                    $boothData = new BoothData($index, $activityuser, $row['hours'], $row['minutes'], $row['datetime'], $row['imageHeightProp'], $row['blurb'], $row['imageTitle'], $row['filetype']);
                    printBoothCell($boothData);
				}
			}
			
			unset($result);
            echo "<hr/>";
		}

        $searchString = $_GET["q"];
        if (startsWith($searchString, "#")) {
            $searchString = substr($searchString, 1, strlen($searchString)-1);
        }
		$sql = "SELECT
					`pkNumber`, 
					`fkUsername`, 
					`blurb`, 
					`imageTitle`, 
					`filetype`, 
					`imageHeightProp`,
					`datetime`, 
					HOUR( timediff( NOW( ) , `datetime` ) ) as `hours`, 
					MINUTE( timediff( NOW( ) , `datetime` ) ) as `minutes` 
				FROM `boothnumbers` b
				WHERE 
				(
					'".$_SESSION['username']."' IN (
					SELECT `fkFriendname` FROM `friendstbl` WHERE `fkUsername` = b.`fkUsername`
					) OR (
						SELECT true FROM `userspublictbl` WHERE `fkUsername` = b.`fkUsername`
					)
				)
				AND `blurb` LIKE '%".mysql_real_escape_string($searchString)."%'
				ORDER BY `datetime` DESC
				LIMIT " . $num * ($_GET['page']-1) . ", ".$num.";";
		$result = mysql_query($sql);
		if(!$result) {
			echo mysql_death1($sql);
		} else {
			if (mysql_num_rows($result) == 0) {
				echo "<i>No booths found...</i>";
			}
			while($row = mysql_fetch_array($result)) {
				$index = $row['pkNumber'];
				$activityuser = $row['fkUsername'];
                $boothData = new BoothData($index, $activityuser, $row['hours'], $row['minutes'], $row['datetime'], $row['imageHeightProp'], $row['blurb'], $row['imageTitle'], $row['filetype']);
				printBoothCell($boothData);
			}
		}
	} else if ($_GET['scope'] == "booth_comment") {
		$sql = "SELECT 
					`pkCommentNumber`, 
					`fkNumber`, 
					`fkUsername`, 
					`commentBody`, 
					`imageHeightProp`,
					`datetime`, 
					HOUR( timediff( NOW( ) , `datetime` ) ) as `hours`, 
					MINUTE( timediff( NOW( ) , `datetime` ) ) as `minutes` 
				FROM `commentstbl` b
				WHERE 
				(
					'".$_SESSION['username']."' IN (
					SELECT `fkFriendname` FROM `friendstbl` WHERE `fkUsername` = b.`fkUsername`
					) OR (
						SELECT true FROM `userspublictbl` WHERE `fkUsername` = b.`fkUsername`
					)
				)
				AND `commentBody` LIKE '%".mysql_real_escape_string($_GET["q"])."%'
				ORDER BY `datetime` DESC
				LIMIT " . $num * ($_GET['page']-1) . ", ".$num.";";
		$result = mysql_query($sql);
		if(!$result) {
			echo mysql_death1($sql);
		} else {			
			if (mysql_num_rows($result) == 0) {
				echo "<i>No booths found...</i>";
			}
			while($row = mysql_fetch_array($result)) {
				$boothindex = $row['fkNumber'];
				$activityuser = $row['fkUsername'];
				$activityindex = $row['pkCommentNumber'];
				debug("printCommentCell(".$boothindex.",".$activityindex.",".$activityuser.",".$row['hours'].",".$row['minutes'].",".$row['datetime']);
				printCommentCell($boothindex, $activityindex, $activityuser, $row['hours'], $row['minutes'], $row['datetime'], true);
			}
		}
	}
	
	echo "
					<hr color = '#EEEEEE'/>
	";
	printClickyButtons($result, "searchresults", "&scope=".urlencode($_GET['scope'])."&q=".urlencode($_GET['q']), $num);
	searchBox();
	
}

function searchBox() {
	echo "
					<p>
					<form action = '/searchresults' method = 'get' style = \"width: 100%;\">
					<input type = \"radio\" name = \"scope\" value = \"user\" checked />Users
	";
	if (isset($_GET['scope']) && $_GET['scope'] == "booth") {
		echo "
					<input type = \"radio\" name = \"scope\" value = \"booth\" checked />Booths
		";
	} else {
		echo "
					<input type = \"radio\" name = \"scope\" value = \"booth\" />Booths
		";
	}
	if (isset($_GET['scope']) && $_GET['scope'] == "booth_comment") {
		echo "
					<input type = \"radio\" name = \"scope\" value = \"booth_comment\" checked />Comments
		";
	} else {
		echo "
					<input type = \"radio\" name = \"scope\" value = \"booth_comment\" />Comments
		";
	}
	echo "
					<div style=\"overflow: hidden; padding-right: .5em;\">
						<input type = 'text' id = 'q' name = 'q' style=\"width: 89%;\" value = \"".$_GET["q"]."\" />
						<input type = 'submit' value = 'Search'  style=\"width: 9%;\" />
					</div>
					</form>
	";
}
