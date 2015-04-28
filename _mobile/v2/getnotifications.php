<?PHP

    require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";
    require_once "{$_SERVER['DOCUMENT_ROOT']}/_mobile/v2/meta/AbstractUserApiResponse.php";
    require_lib("h2o-php/h2o");
    require_common("db");

class NotificationsResponse extends AbstractUserApiResponse {

    protected function run($username)
    {
        $output = array();
        // Currently, this just contains the "you have new friend request notification", but it will also include site
        // news, mentions, etc.
        $sqlBuilder = new h2o("{$_SERVER['DOCUMENT_ROOT']}/_mobile/v2/queries/getUncheckedFriendRequests.mst.sql");
        $sql = $sqlBuilder->render(array("username" => $username));
        $dblink = connect_boothDB();
        $query = $dblink->query($sql);
        $num = sql_get_expectOneRow($query, "count");
        if ($num > 0) {
            $output[] = array(
                "text" => "You have new friend requests",
                "type" => "friend_requests",
                "url" => base()."/users/".$username."/friends/manage"
            );
        }

        $this->markCallAsSuccessful("Notifications get OK", array("data" => $output));

    }
}

$response = new NotificationsResponse();
$response->runAndEcho();
