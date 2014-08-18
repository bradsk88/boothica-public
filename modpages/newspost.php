<?PHP

require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";
require_page("Post");
require_mod_asset("NewsCell");
require_mod_asset("NewsCommentInputSection");

class NewsPost extends Post {

    protected function headScripts()
    {
        echo "
            <script type = 'text/javascript' src = '/comments/base.js'></script>
            <script type = 'text/javascript' src = '/comments/input.js'></script>
			<script type = 'text/javascript' src = '/news_comments.js'></script>
			<script type = 'text/javascript'>
			window.onload = new function() {

                update_comments();

            }
			</script>
			<link rel='stylesheet' href='/css/commentinput.css' type='text/css' media='screen' />
			<link rel='stylesheet' href='/css/comments.css' type='text/css' media='screen' />
			<link rel='stylesheet' href='/css/booths.css' type='text/css' media='screen' />
	";
    }

    protected function preScripts()
    {
        // TODO: Implement preScripts() method.
    }

    private function printBooth() {

        $newsnumber = $_GET['n'];
        $boothsql = "SELECT
                    `body`,
                    `title`,
                    `fkUsername`,
                    `postTime`,
                    HOUR( timediff( NOW( ) , `postTime` ) ) as `hours`,
                    MINUTE( timediff( NOW( ) , `postTime` ) ) as `minutes`
                    FROM `newstbl`
                    WHERE `pkIndex`=".$newsnumber."
                    LIMIT 1;";
        $postquery = mysql_query($boothsql);

        if ($postquery) {

            if (mysql_num_rows($postquery) == 1) {

                $row = mysql_fetch_array($postquery);
                echo "
                    <div class = 'blurb'>
                ";
                $datetime = $row['postTime'];
                $poster = $row['fkUsername'];
                $postTitle = $row['title'];
                $postBody = $row['body'];
                $newsCell = new NewsCell($poster, $newsnumber, $datetime, $postBody, $postTitle, true);
                echo $newsCell."<br/>
                ";

                //printBlurbButtons();

                echo "
                    </div>
                    <hr color=#EEEEEE />
                ";

            } else {

                echo "Blurb failed to load\n";

            }

            echo "
                    <div id = 'comments'>\n
                        <div class = 'contentheaderbar'>Loading comments...</div>
                    </div>";
        } else {
            echo "
            <div id = 'comments'>\n
                        <div class = 'contentheaderbar'>".mysql_death1($boothsql)."</div>
            </div>";
        }
    }

    function printLikes() {

        $sql = "SELECT SUM(`value`) as `num` FROM `likes_boothstbl`
                WHERE `fkBoothNumber` = ".$_GET['n'].";";
        $query = mysql_query($sql);
        if($query) {
            $row = mysql_fetch_array($query);
            echo "
                <span id = \"likesnum\">
            ";
            if ($row['num'] > 0) {
                echo "+".$row['num']." Likes";
            } else if ($row['num'] < 0) {
                echo "-".$row['num'];
            }
            echo "
                </span>
            ";
        }else {
            echo mysql_death1($sql);
        }
    }

    function printBlurbButtons() {

        $newsnumber = $_GET['n'];
        //TODO: Generalize this for other news posters.
        $boother = 'bradsk88';
        echo "
                                <hr color = '#FFFFFF'/>
                                <div class = 'leftblurbbuttons'>
        ";
        printLikes();
        echo "
                                </div>
        ";

        if (isset($_SESSION['username'])) {
            if ($boother == $_SESSION['username']) {

                echo "
                                <div class = 'blurbbuttons'>
                ";
                if (!isBanned($boother) && !isSuspended($boother)) {
                    echo "
                                    <a href = 'javascript:like_booth(".$newsnumber . ")'>
                                        <img src= \"/media/thumbzup.png\" title = \"I like this!\" onclick=\"this.src='/media/thumbzup_y.png'\">
                                    </a>
                                    <a href = 'javascript:editBlurb()'>
                                        <img src= \"/media/edit.png\" title = \"edit\">
                                    </a>
                    ";
                }
                echo "
                                    <a href = 'javascript:deleteBooth(".$newsnumber.")'>
                                        <img src= \"/media/delete.png\" title = \"delete\">
                                    </a>
                                </div>
                ";

            } else {

                echo "
                                <div class = 'blurbbuttons'>
                                    <a href = 'javascript:like_booth(".$newsnumber . ")'>
                                        <img src= \"/media/thumbzup.png\" title = \"I like this!\" onclick=\"this.src='/media/thumbzup_y.png'\">
                                    </a>
                ";
                if (isModerator($_SESSION['username'])) {
                    echo "
                                    <a href = 'javascript:modDeleteBooth(".$newsnumber.", \"".$boother."\")'>
                                        <img src= \"/media/delete.png\" title = \"delete\">
                                    </a>
                    ";
                }
                echo "
                                </div>
                ";

            }
        }
    }

    function doShow() {
        $link = connect_to_boothsite();

        if (! isset($link)) {
            throw new Exception("Database connection does not exist.");
        }

        $this->showContent();

    }

    public function showContent()
    {
        $this->printBooth();
        $newsnumber = $_GET['n'];
        echo "
					<iframe id='comment_target' name='comment_target' src='/comment/target.html' style='width:100%;height:0px;border:0px solid #FFF;'>
					</iframe>
					<div id = 'commentstatus'></div>
					<div id = 'commentinputsection' style='width:100%; height: 180px; position:relative'>
			";

        echo NewsCommentInputSection::newInstance($newsnumber, "bradsk88");

        echo "
					</div>
			";
            //$this->showComments($urlparts);
    }

    /**
     * @param $urlparts
     */
    public function showComments($urlparts)
    {
        $newsnumber = $_GET['n'];
        echo "
                        <iframe id='comment_target' name='comment_target' src='/comment/target.html' style='width:100%;height:0px;border:0px solid #FFF;'>
                        </iframe>
                        <div id = 'commentstatus'></div>
                        <div id = 'commentinputsection' style='width:100%; height: 180px; position:relative'>
                ";

        $_GET['boother'] = $urlparts[2];
        $_GET['number'] = $newsnumber;
        include("{$_SERVER['DOCUMENT_ROOT']}/commentinputsection.php");

        echo "
                        </div>
                ";
    }
}
