<?PHP

function printUserNavBar($mode,$displayname) {
	printPrivateUserNavBar($mode,$displayname,false);
}

final class NFPageMode {
    const BOOTHS = 0;
    const FRIENDS = 1;
    const QUILT = 2;
    const ABOUT = 3;
    const PM = 4;
    const NONE = -1;
}

function printNotLoggedInPublicBar($bootherdisplayname) {
    printNotLoggedInBar($bootherdisplayname, false);
}


function printNotLoggedInPrivateBar($bootherdisplayname) {
    printNotLoggedInBar($bootherdisplayname, true);
}

function printNotLoggedInBar($bootherdisplayname, $isPrivate) {
    echo "
							<div class = 'contentheader'>
								<div class = 'contentheaderbar'>
	";
    if ($isPrivate) {
        echo "
                                    <div class = 'friendbartitleprivate'>
									<div class = 'friendbartitlelabel'>
											".$bootherdisplayname."'s account is private
										</div>
									</div>
    ";
    } else {
        echo "
									<div class = 'friendbartitlepublic'>
									<div class = 'friendbartitlelabel'>
											".$bootherdisplayname."'s account is public
										</div>
									</div>
    ";
    }
    echo "
									<a href = '/login'>
	".buttonDivTop(true).
        "
											Click here to log in
										</div>
									</a>
                                    </div>
                                </div>
        ";
}

function printNotFriendBar($bootherdisplayname, $pageMode) {
    $boothername = strtolower($bootherdisplayname);
    debug($pageMode);

    echo "
							<div class = 'contentheader'>
								<div class = 'contentheaderbar'>
									<div class = 'friendbartitlepublic'>
									<div class = 'friendbartitlelabel'>
											".$bootherdisplayname." is not your friend
										</div>
									</div>
    ";
    friendRequestButton($boothername);
    echo "
									<a href = '/users/".$boothername. "/booths'>
	".buttonDivTop($pageMode == NFPageMode::BOOTHS).
        "
											Booths
										</div>
									</a>
									<a href = '/users/".$boothername. "/friends'>
    ".buttonDivTop($pageMode == NFPageMode::FRIENDS).
        "
                                                Friends
                                            </div>
                                        </a>
                                        <a href = '/users/".$boothername. "/quilt'>
    ".buttonDivTop($pageMode == NFPageMode::QUILT).
        "
                                                Quilt
                                            </div>
                                        </a>
                                        <a href = '/users/".$boothername. "/about'>
    ".buttonDivTop($pageMode == NFPageMode::ABOUT).
        "
                                                About Me
                                            </div>
                                        </a>
                                        <a href = '/messages/message?rcv=".$boothername."'>
    ".buttonDivTop($pageMode == NFPageMode::PM).
        "
                                            Send PM
                                        </div>
                                    </a>
                                    </div>
                                </div>
        ";
}

/**
 * @param $boothername
 */
function friendRequestButton($boothername)
{
    $requestsql = "SELECT
				`fkUsername`
				FROM `friendstbl`
				WHERE `fkUsername` = '" . $_SESSION['username'] . "'
				AND `fkFriendName` = '" . $boothername . "'
				LIMIT 1;";
    $requestres = mysql_query($requestsql);
    if (mysql_num_rows($requestres) == 1) {
        echo "
										<a href=/users/" . $_SESSION['username'] . "/friendrequests><div class = 'friendrequestdonebutton'>Request sent</div></a>

		";
    } else {
        echo "
										<a href=/actions/request?username=" . $boothername . "><div class = 'friendrequestbutton'>Send friend request</div></a>
		";
    }
}

function printNotUserBar($bootherdisplayname, $pageMode) {
    $boothername = strtolower($bootherdisplayname);
    debug($pageMode);
    echo "
							<div class = 'contentheader'>
								<div class = 'contentheaderbar'>
									<div class = 'friendbartitlefriend'>
									<div class = 'friendbartitlelabel'>
											".$bootherdisplayname."
										</div>
									</div>
									<a href = '/users/".$boothername. "/friends'>
    ".buttonDivTop($pageMode == NFPageMode::BOOTHS).
        "
                                                Booths
                                            </div>
                                        </a>
                                        <a href = '/users/".$boothername. "/friends'>
    ".buttonDivTop($pageMode == NFPageMode::FRIENDS).
    "
                                            Friends
										</div>
									</a>
									<a href = '/users/".$boothername. "/quilt'>
    ".buttonDivTop($pageMode == NFPageMode::QUILT).
    "
                                            Quilt
                                        </div>
                                    </a>
                                    <a href = '/users/".$boothername. "/about'>
    ".buttonDivTop($pageMode == NFPageMode::ABOUT).
    "
											About Me
										</div>
									</a>
									<a href = '/messages/message?rcv=".$boothername."'>
    ".buttonDivTop($pageMode == NFPageMode::PM).
    "
										Send PM
									</div>
								</a>
								</div>
							</div>
	";
}

function s($boothername) {
    $lastLetter = substr($boothername, strlen($boothername) - 1, 1);
    if (strtolower($lastLetter) === "s") {
        return "'";
    }
    return "'s";
}

function printPrivateNotFriendBar($bootherdisplayname) {
    $boothername = strtolower($bootherdisplayname);
    echo "
							<div class = 'contentheader'>
								<div class = 'contentheaderbar'>
									<div class = 'friendbartitlestranger'>
									<div class = 'friendbartitlelabel'>
											".$bootherdisplayname.s($boothername)." account is private

										</div>
									</div>
    ";
    friendRequestButton($boothername);
    echo "
                                        <a href = '/users/".$boothername. "/about'>
    ".buttonDivTop(false).
        "
                                                About Me
                                            </div>
                                        </a>
                                        <a href = '/messages/message?rcv=".$boothername."'>
    ".buttonDivTop(false).
        "
                                            Send PM
                                        </div>
                                    </a>
                                    </div>
                                </div>
        ";
}


function buttonDivTop($isDown) {
    if ($isDown) {
        return "                                <div class = 'friendbarbuttondown'>";
    }
    return "                                <div class = 'friendbarbuttonup'>";
}

function printPrivateUserNavBar($mode,$displayname,$isprivate) {

	if (!isset($_SESSION['username'])) {
		return;
	}
		
	$username = $_SESSION['username'];
	if (isset($urlparts) && count($urlparts) == 3) {
		$username = $urlparts[2];
	}
		
	$class_mentions = "friendbarbuttonup";	
	$class_friendbooths = "friendbarbuttonup";
	$class_friends = "friendbarbuttonup";
	$class_activity = "friendbarbuttonup";
    $class_quilt = "friendbarbuttonup";
	$class_today = "friendbarbuttonup";
	$class_requests = "friendbarbuttonup";
	$class_ignores = "friendbarbuttonup";
	$class_about = "friendbarbuttonup";
	$class_search = "friendbarbuttonup";
	
	debug($mode);
	if ($mode == 'mentions') {
		$class_mentions = "friendbarbuttondown";
	}
	if ($mode == 'listfriendsbooths') {
		$class_friendbooths = "friendbarbuttondown";		
	}
	if ($mode == 'listfriends') {
		$class_friends = "friendbarbuttondown";
	}
	if ($mode == 'friendsactivity') {
		$class_activity = "friendbarbuttondown";
	}
    if ($mode == 'quilt') {
        $class_quilt = "friendbarbuttondown";
    }
	if ($mode == 'today') {
		$class_today = "friendbarbuttondown";
	}
	if ($mode == 'listfriendrequests') {
		$class_requests = "friendbarbuttondown";
	}
	if ($mode == 'listignores') {
		$class_ignores = "friendbarbuttondown";
	}
	if ($mode == 'about') {
		$class_about = "friendbarbuttondown";
	}
	if ($mode == 'search') {
		$class_search = "friendbarbuttondown";
	}
		
			echo "						
					<div class = 'contentheaderbarregion'>
						<div class = 'contentheader'> 
							<div class = 'contentheaderbar'>	
								<div class = 'friendbartitlecuruser'>
			";
			if ($isprivate) {
				echo "
									<div class = 'friendbartitlecuruserprivate'>
										<div class = 'eyeicon' title = 'Only your friends can see this page'>
										</div>
				";
			} else {
				echo "
									<div class = 'friendbartitlelabel'>
				";
			}
			echo "
										".$displayname."
									</div>
								</div>
								<a href = '/users/".$username."/mentions'>
									<div class = \"".$class_mentions."\">
										Mentions
									</div>
								</a>
								<a href='/users/".$username."/friendsbooths'>
									<div class = \"".$class_friendbooths."\">
										Friends' Booths
									</div>
								</a>
								<a href='/users/".$username."/friends'>
									<div class = \"".$class_friends."\">
										Friends
									</div>
								</a>
								<a href='/users/".$username."/friendsactivity'>
									<div class = \"".$class_activity."\">
										Activity
									</div>
								</a>
								<a href = '/users/".$username."/today'>
									<div class = \"".$class_today."\">
										Today
									</div>
								</a>
								<a href = '/users/".$username."/quilt'>
									<div class = \"".$class_quilt."\">
									    Quilt
									</div>
								</a>
								<a href='/users/".$username."/friendrequests'>
									<div class = \"".$class_requests."\">
										Requests
									</div>
								</a>
								<a href='/users/".$username."/ignores'>
									<div class = \"".$class_ignores."\">
										Ignores
									</div>
								</a>
								<a href='/users/".$username."/about'>
									<div class = \"".$class_about."\">
										About Me
									</div>
								</a>
								<a href = '/search'>
									<div class = \"".$class_search."\">
										Search
									</div>
								</a>
							</div>
						</div>
					</div>";
}

function printAboutMe($boothername) {

	echo "
				<div width = 100%>
					<div class = 'booth' style='background-image: url(".getPublicImage($boothername).");'>
						<div class = 'bootherlink'>About ".getDisplayName($boothername)."</div>
					</div>
	";
	if ($_SESSION['username'] == $boothername) {
		echo "
					<a href = \"/account/publiciconcapture\"><div class = 'underboothlink'>Change your public image</div></a>
					<a href = \"/account/aboutme\"><div class = 'underboothlink'>Change your \"About Me\" text</div></a>
		";
	}
	echo "
				</div>
				<div class = 'blurb'>
	";
	$urlparts = explode( '/', $_SERVER['REQUEST_URI'] );
	$pagenameparts = explode( '.', $urlparts[3] );
	$boothnumber = $pagenameparts[0];
	$boothsql = "SELECT 
				`about`
				FROM `usersabouttbl` 
				WHERE `fkUsername`='".$urlparts[2]."'
				LIMIT 1;";
	$boothquery = mysql_query($boothsql);
	if (!$boothquery) {
		echo mysql_death1($boothsql);
	} else {
		$row = mysql_fetch_array($boothquery);
		echo nl2br($row['about']);
	}
	echo "
				</div>
	";

}

function getBooths($boothername, $pageNum, $perPage) {

    return mysql_query(getBoothsSQL($boothername, $pageNum, $perPage));
}

function getBoothsSQL($boothername, $pageNum, $perPage) {

    $startnum = $perPage * ($pageNum-1);
    $sql = "SELECT
			`pkNumber`,
			`fkUsername`,
			`blurb`,
			`datetime`,
			`imageTitle`,
			`filetype`,
			`imageHeightProp`
			FROM `boothnumbers`
			WHERE `fkUsername` ='" . $boothername . "'
    ";
    if (!isFriendOf($_SESSION['username'], $boothername)) {
        $sql .= " AND `isPublic` = true ";
    }
    $sql .= "ORDER BY `pkNumber` DESC
			LIMIT " . $startnum . ", " . $perPage . ";";
    return $sql;
}

function getSizeForQuilt($numberOfBooths) {
    if ($numberOfBooths <= 1) {
        return 800;
    }
    else if ($numberOfBooths <= 4) {
        return 400;
    }
    else if ($numberOfBooths <= 6) {
        return 266;
    }
    else if ($numberOfBooths <= 12) {
        return 200;
    }
    else if ($numberOfBooths <= 25) {
        return 160;
    }
    else if ($numberOfBooths <= 36) {
        return 133;
    }
    else if ($numberOfBooths <= 49) {
        return 114;
    }
    return 100;
}
