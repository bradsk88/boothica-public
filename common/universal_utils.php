<?PHP

function printTwitterLink() {
echo "
			<a href=\"https://twitter.com/BoothicaNews\" class=\"twitter-follow-button\" data-show-count=\"false\" data-size=\"large\">Follow @BoothicaNews</a>
			<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=\"//platform.twitter.com/widgets.js\";fjs.parentNode.insertBefore(js,fjs);}}(document,\"script\",\"twitter-wjs\");</script>
";

}

function printTwitterLinkWithText($text) {
echo "
			<a href=\"https://twitter.com/BoothicaNews\" class=\"twitter-follow-button\" data-show-count=\"false\" data-size=\"large\">".$text."</a>
			<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=\"//platform.twitter.com/widgets.js\";fjs.parentNode.insertBefore(js,fjs);}}(document,\"script\",\"twitter-wjs\");</script>
";

}

function print404() {
    $url = substr($_SERVER['REQUEST_URI'], 0, 32);
    if (strlen($_SERVER['REQUEST_URI']) > 32) {
        $url = $url."...";
    }
    echo "
            <div class=\"lightlink\">
                <div class = 'smallcenteredcontent' align = center>
                    <div class = 'smallcontentarea'>
                        <div class = 'row'>
                            <h3>The address <b>".$url."</b> is not a valid page.</h3><br/>
                        </div>
                        <div class = 'row'>
                            <a href = '/publicfeed'>
                                <span class = 'navbutton'>
                                    View the Live Feed
                                </span>
                            </a>
                            <a href = '/'>
                                <span class = 'navbuttonend'>
                                    Boothi.ca Homepage
                                </span>
                            </a>
                        </div>
                        <div class = 'row'>
                            Search for users:
                        </div>
                        <div class = 'row'>
                            <form action = '/searchresults' method = 'get'>
                                <input type = 'text' name = 'q'/><input type = 'submit' value = 'Search'>
                                <input type = 'hidden' name = 'scope' value = 'user'/>
                            </form>
                        </div>
                        <div class = 'row'>
                            <small><a class=\"lightlink\" href = \"/info/reportform?type=404&dest=".urlencode("http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'])."&source=".urlencode($_SERVER['HTTP_REFERER'])."\">Report Broken Link</a></small>
                        </div>
                    </div>
                </div>
            </div>
		</div>
	";
}

function print404Page() {

    include("{$_SERVER['DOCUMENT_ROOT']}/content/html.php");
    include("{$_SERVER['DOCUMENT_ROOT']}/common/smallpage_top.php");
    include("{$_SERVER['DOCUMENT_ROOT']}/common/header.php");
    print404();
    include("{$_SERVER['DOCUMENT_ROOT']}/common/header.php");
    echo "
    </body>
</html>
";
}

function printBanned() {
	echo "
				<div class = 'contentheaderbarregion'>
					<div class = 'contentheaderbar'>
						<div class = 'friendstatus'>
							<div style = 'position: absolute; top: 5px'>
								Access Denied
							</div>
						</div>
					</div>
				</div>
				You are banned
	";
}

function printLogin() {
	echo "<div class = 'contentheaderbarregion'>
			<div class = 'contentheaderbar'>
				<div class = 'friendstatus'>
					<div style = 'position: absolute; top: 5px'>
						Access Denied
					</div>
				</div>
			</div>
			</div>
	";
    printLoginMessage();
}

function printLoginMessage() {
    echo "You must be logged in to access this area";
}

function printIA() {
	echo "<div class = 'contentheaderbarregion'>
			<div class = 'contentheaderbar'>
				<div class = 'friendstatus'>
					<div style = 'position: absolute; top: 5px'>
						Access Denied
					</div>
				</div>
			</div>
			</div>
			This page was not meant to be accessed this way.
	";
}

