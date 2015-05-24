<?PHP

    require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";
    require_once "{$_SERVER['DOCUMENT_ROOT']}/_mobile/v2/meta/AbstractUserApiResponse.php";
    require_lib("h2o-php/h2o");
    require_common("db");

class NotificationsResponse extends AbstractUserApiResponse {

    protected function run($username)
    {
        $output = array();

        // Friend requests
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

        // Private messages
        $sqlBuilder = new h2o("{$_SERVER['DOCUMENT_ROOT']}/_mobile/v2/queries/getUnreadPrivateMessageCount.mst.sql");
        $sql = $sqlBuilder->render(array("username" => $username));
        $dblink = connect_boothDB();
        $query = $dblink->query($sql);
        $num = sql_get_expectOneRow($query, "count");
        if ($num > 0) {
            $output[] = array(
                "text" => "You have new private messages",
                "type" => "private_messages",
                "url" => base()."/pm"
            );
        }

        // Private messages
        $sqlBuilder = new h2o("{$_SERVER['DOCUMENT_ROOT']}/_mobile/v2/queries/getUncheckedMentionsCount.mst.sql");
        $sql = $sqlBuilder->render(array("username" => $username));
        $dblink = connect_boothDB();
        $query = $dblink->query($sql);
        $num = sql_get_expectOneRow($query, "count");
        if ($num > 0) {
            $output[] = array(
                "text" => "You have been mentioned",
                "type" => "mentions",
                "url" => base()."/mentions"
            );
        }

        $this->markCallAsSuccessful("Notifications get OK", array("data" => $output));

    }
}

$response = new NotificationsResponse();
$response->runAndEcho();
