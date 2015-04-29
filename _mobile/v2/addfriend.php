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
        $otherUser = $_POST['boothername'];
        if (!userExists($otherUser)) {
            $this->markCallAsFailure("User ". $otherUser ." does not exist.");
            return;
        }

        if (isFriendOf($otherUser, $_SESSION['username'])) {
            $this->markCallAsSuccessful("Already sent friend request to ".getDisplayName($otherUser));
            return;
        }

        $sqlBuilder = new h2o("{$_SERVER['DOCUMENT_ROOT']}/_mobile/v2/queries/addFriend.mst.sql");
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

        $this->markCallAsSuccessful("Friend request to ".getDisplayName($otherUser)." sent successfully.");

        $email = getEmail($otherUser);
        $msg = "You have a new friend request"; //TODO: Write this email message
        sendBoothicaEmail($email, "Friend Request: ".getDisplayName($username), $msg);
        return;

    }
}

$action = new AddFriendResponse();
$action->runAndEcho();
