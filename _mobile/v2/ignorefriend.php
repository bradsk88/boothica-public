<?PHP

require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/_mobile/v2/meta/AbstractUserApiResponse.php";
require_lib("h2o-php/h2o");
require_common("utils");

class AddFriendResponse extends AbstractUserApiResponse {

    function __construct() {
        parent::__construct(array('boothername'));
    }

    protected function run($username)
    {
        if ($this->fail_if_the_end()) {
            return;
        }

        $otherUser = $_POST['boothername'];
        if (!userExists($otherUser)) {
            $this->markCallAsFailure("User ". $otherUser ." does not exist.");
            return;
        }

        if (!isFriendOf($_SESSION['username'], $otherUser)) {
            $this->markCallAsFailure("Cannot ignore a request that hasn't been made".getDisplayName($otherUser));
            return;
        }

        $sqlBuilder = new h2o("{$_SERVER['DOCUMENT_ROOT']}/_mobile/v2/queries/ignoreFriend.mst.sql");
        $sql = $sqlBuilder->render(array(
            "username" => $username,
            "friendUsername" => $otherUser
        ));
        $dblink = connect_boothDB();
        $query = $dblink->query($sql);
        if (!$query) {
            $this->markCallAsFailure(sql_death1($sql));
            return;
        }

        $this->markCallAsSuccessful("Friend request from ".getDisplayName($otherUser)." ignored successfully.");
        return;

    }
}

$action = new AddFriendResponse();
$action->runAndEcho();
