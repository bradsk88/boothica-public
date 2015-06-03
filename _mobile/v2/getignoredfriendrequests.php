<?PHP

require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/_mobile/v2/meta/AbstractUserApiResponse.php";
require_lib("h2o-php/h2o");
require_common("db");
require_asset("UserImage");

class GetIgnoredFriendRequestResponse extends AbstractUserApiResponse {

    protected function run($username) {
        $sqlBuilder = new h2o("{$_SERVER['DOCUMENT_ROOT']}/_mobile/v2/queries/getIgnoredFriendRequests.mst.sql");
        $sql = $sqlBuilder->render(array("username" => $username));

        $dblink = connect_boothDB();
        $query = $dblink->query($sql);
        if (!$query) {
            $this->markCallAsFailure(sql_death1($sql));
            return;
        }

        $requests = array();
        while ($row = $query->fetch_array()) {
            $requests[] = array(
                'username' => $row['username'],
                'displayName' => (string) getDisplayName($row['username']),
                'datetime' => $row['datetime'],
                'userImageAbsoluteUrl' => UserImage::getAbsoluteImage($row['username'])
            );
        }
        $this->markCallAsSuccessful("Ignored friend requests get OK", array("requests" => $requests));

    }
}

$response = new GetIgnoredFriendRequestResponse();
$response->runAndEcho();
