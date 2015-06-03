<?PHP

require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";
require_once("{$_SERVER['DOCUMENT_ROOT']}/_mobile/v2/meta/AbstractUserApiResponse.php");
require_common("utils");

/**
 * Class ApiUserResponse
 * A "user response" is an API response which requires that the user is logged in.
 */
abstract class AbstractPublicApiResponse extends AbstractUserApiResponse {

    private $username;

    function __construct($requiredArgs=array()) {
        parent::__construct($requiredArgs);
    }

    protected function run($username) {
        $this->username = $username;
        $this->runMaybeLoggedIn();
    }

    protected abstract function runMaybeLoggedIn();

    protected function getUsernameIfSet() {
        return $this->username;
    }

    protected function requiresLogin() {
        return False;
    }

}
