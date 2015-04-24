<?PHP

require_once("{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php");
require_common("db");
require_common("utils");
require_once("{$_SERVER['DOCUMENT_ROOT']}/_mobile/v2/meta/AbstractUserApiResponse.php");
require_lib("h2o-php/h2o");
require_asset("UserImage");

class FriendListActivity extends AbstractUserApiResponse {
    protected function run($username)
    {
        if (!isLoggedIn()) {
            echo json_encode(array(
                "error" => "Must be logged in to view friend lists."
            ));
            return;
        }

        if (parameterIsMissingAndEchoFailureMessage('username')) {
            return;
        }
        $boothername = $_REQUEST['username'];

        if (doesUserAppearPrivate($username)) {
            echo json_encode(array(
                "error" => getDisplayName($username)." is not allowed to view ".getPossessiveDisplayName($boothername)." friends."
            ));
            return;
        }

        $sqlBuilder = new h2o("{$_SERVER['DOCUMENT_ROOT']}/_mobile/v2/queries/friendList.mst.sql");

        $sql = $sqlBuilder->render(array(
            "bootherName" => $boothername
        ));

        $dblink = connect_boothDB();
        $query = $dblink->query($sql);
        $boothers = array();
        while ($row = $query->fetch_array()) {
            $boothers[] = array(
                "bootherImageUrl" => UserImage::getAbsoluteImage($row['username']),
                "bootherDisplayName" => (string) getDisplayName($row['username']),
                "bootherName" => $row['username']
            );
        }

        echo json_encode(array(
            "success" => array(
                "boothers" => $boothers
            )
        ));
    }
}

$activity = new FriendListActivity();
$activity->runAndEcho();
