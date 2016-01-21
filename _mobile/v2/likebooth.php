<?php

require_once("{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/_mobile/utils.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/_mobile/v2/meta/AbstractUserApiResponse.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/comment/CommentObj.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/comment/Comments.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/booth/utils.php");
require_common("utils");
require_common("internal_utils");

class LikeBoothApiResponse extends AbstractUserApiResponse {
    function __construct() {
        parent::__construct(array(
            "boothnum"
        ));
    }

    protected function run($username)
    {
        if ($this->fail_if_the_end()) {
            return;
        }

        $boothnum = $_REQUEST['boothnum'];
        if (!isAllowedToInteractWithBooth($username, $boothnum)) {
            $this->markCallAsFailure($username . " is not allowed to interact with booth " . $boothnum);
            return;
        }

        $dblink = connect_boothDB();
        $sql = "REPLACE INTO `likes_boothstbl`
			(`fkBoothNumber`, `fkUsername`, `value`)
			VALUES
			('" . $boothnum . "','" . $username . "', 1);";
        $result = $dblink->query($sql);
        if (!$result) {
            $this->markCallAsFailure(sql_death1($sql));
            return;
        }
        $sql = "SELECT
			COUNT(*) as `num`
			FROM `likes_boothstbl`
			WHERE `fkBoothNumber` = " . $boothnum . ";";
        $numres = $dblink->query($sql);
        if (!$numres) {
            $this->markCallAsFailure(sql_death1($sql));
            return;
        }
        $row = $numres->fetch_array();
        $this->markCallAsSuccessful(array(
            "new_num_likes" => $row['num']
        ));
        return;
    }
}

$response = new LikeBoothApiResponse();
$response->runAndEcho();
