<?PHP

require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";
require_common("db");
require_asset("UserImage");
require_asset("BoothImage");
require_lib("h2o-php/h2o");
require_once "{$_SERVER['DOCUMENT_ROOT']}/booth/utils.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/_mobile/v2/meta/AbstractUserApiResponse.php";

class ActivityResponse extends AbstractUserApiResponse {

    /**
     * This should be implemented by descendants but should not be called directly.  Use runAndEcho.
     */
    protected function run($username)
    {
        $items = $this->getItemsMethod1($username);
        $this->markCallAsSuccessful("Activity get OK", array("data" => $items));
    }

    /**
     * This function uses one large SQL query to get all of the activity items for a user's activity feed.
     */
    private function getItemsMethod1($username, $pageNum=1, $numPerPage=9) {
        $dblink = connect_boothDB();

        if ($this->userHasNoFriends($username)) {
            return array();
        }

        $sqlBuilder = new h2o("{$_SERVER['DOCUMENT_ROOT']}/_mobile/v2/queries/activity.mst.sql");

        $sql = $sqlBuilder->render(array(
            "username"=>$dblink->real_escape_string($username),
            "pageStartIndex" => $numPerPage * ($pageNum - 1),
            "numPerPage"=>$dblink->real_escape_string($numPerPage)
        ));

        $result = $dblink->query($sql);
        if (!$result) {
            sql_death1($sql);
            return array();
        }

        $output = array();
        while ($row = $result->fetch_array()) {
            $commentImage = $row['ext'] ? base()."/comments/".$row['media'] : base()."/media/error.png";
            $canSee = isAllowedToInteractWithBooth($username, $row['boothNum']);
            $output[] = array(
                "currentUserName" => $username
                , "commentText" => $row['commentText']
                , "commenterName" => $row['commenterName']
                , "commenterDisplayName" => (string) getDisplayName($row['commenterName'])
                , "commenterImage" => UserImage::getAbsoluteImage($row['commenterName'])
                , "bootherName" => $row['bootherName']
                , "bootherDisplayName" => (string) getDisplayName($row['bootherName'])
                , "bootherImage" => BoothImage::getAbsoluteImage($row['boothNum'], $row['bootherName'])
                , "boothNum" => $row['boothNum']
                , "hasMedia" => $canSee ? $row['hasMedia'] == 1 : false
                , "commentMediaImage" => $commentImage
            );
        }
        return $output;

    }

    private function userHasNoFriends($username) {
        $dblink = connect_boothDB();
        $sqlBuilder = new h2o("queries/friendcount.mst.sql");
        $sql = $sqlBuilder->render(array("username" => $username));
        $result = $dblink->query($sql);
        if (!$result) {
            sql_death1($sql);
            return true;
        }
        if ($result->num_rows==0) {
            return true;
        }
        $array = $result->fetch_array();
        $count = $array[0];
        if ($count == 0) {
            return true;
        }
        return false;
    }

}

$response = new ActivityResponse();
$response->runAndEcho();
