<?PHP

require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";
require_common("utils");

/**
 * Class ApiUserResponse
 * A "user response" is an API response which requires that the user is logged in.
 */
abstract class AbstractUserApiResponse {

    protected $requiredArgs;

    function __construct($requiredArgs=array()) {
        $this->requiredArgs = $requiredArgs;
    }

    function runAndEcho() {
        foreach ($this->requiredArgs as $arg) {
            if (parameterIsMissingAndEchoFailureMessage($arg)) {
                return;
            }
        }
        $username = null;
        if ($this->requiresLogin() && ($username = $this->getUsername()) == null) {
            return;
        }
        $this->run($username);
    }

    protected function requiresLogin() {
        return true;
    }

    /**
     * This should be implemented by descendants but should not be called directly.  Use runAndEcho.
     */
    protected abstract function run($username);

    private function getUsername() {
        if (isLoggedIn()) {
            return $_SESSION['username'];
        }
        $username = isset($_POST['username']) ? $_POST['username'] : null;
        if (isset($_POST['username']) && failsStandardMobileChecksAndEchoFailureMessage()) {
            return null;
        }

        if (!$username) {
            echo json_encode(
                array(
                    "error" => "Must have active session or API credentials"));
            return null;
        }

        if (!userExists($username)) {
            echo json_encode(
                array(
                    "error" => "Current user '" . $username . "' does not exist"));
            return null;
        }

        if (isBanned($username)) {
            echo json_encode(
                array(
                    "error" => "User is banned"));
            return null;
        }


        $_SESSION['username'] = $username;
        return $username;
    }

}
