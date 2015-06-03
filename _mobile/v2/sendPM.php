<?PHP

require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/_mobile/v2/meta/AbstractUserApiResponse.php";
require_lib("h2o-php/h2o");
require_common("utils");
require_common("internal_utils");
require_common("db");

class SendPMResponse extends AbstractUserApiResponse {

    function __construct() {
        parent::__construct(array('otherUsername', 'text'));
    }

    protected function run($username)
    {
        $otherUser = $_POST['otherUsername'];
        if (!userExists($otherUser)) {
            $this->markCallAsFailure("User ". $otherUser ." does not exist.");
            return;
        }

        $sqlBuilder = new h2o("{$_SERVER['DOCUMENT_ROOT']}/_mobile/v2/queries/putPM.mst.sql");
        $processed = preProcessPrivateMessage($_POST['text']);
        $encryptPrivateMessage = encryptPrivateMessage($processed);

        $dblink = connect_boothDB();

        $sql = $sqlBuilder->render(array(
            "username" => $dblink->real_escape_string($username),
            "otherUsername" => $dblink->real_escape_string($otherUser),
            "message" => $dblink->real_escape_string($encryptPrivateMessage)
        ));



        $query = $dblink->query($sql);
        if (!$query) {
            $this->markCallAsFailure(sql_death1($sql));
            return;
        }

        $this->markCallAsSuccessful("Private message to ".getDisplayName($otherUser)." sent successfully.");
        return;

    }
}

$action = new SendPMResponse();
$action->runAndEcho();
