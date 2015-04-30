<?PHP

require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/_mobile/utils.php";
require_common("utils");


/**
 * Class ApiUserResponse
 * A "user response" is an API response which requires that the user is logged in.
 */
abstract class AbstractUserApiResponse {

    protected $requiredArgs;

    private $successMessage = "Api call was successful";
    private $errorMessage = "Api call failed for unknown reason";
    private $additionalSuccessData = array();

    protected $callWasSuccessful = false;

    function __construct($requiredArgs=array()) {
        $this->requiredArgs = $requiredArgs;
        if (in_array('username', $requiredArgs)) {
            death("Somebody used 'username' as a required arg");
            throw new Exception("username is a reserved argument for APIs.");
        }
    }

    function runAndEcho() {
        foreach ($this->requiredArgs as $arg) {
            if (parameterIsMissingAndEchoFailureMessage($arg)) {
                return;
            }
        }
        $username = null;
        if ($this->requiresLogin()) {
            $username = $this->getUsername();
            if ($username == null) {
                return;
            }
            if (isBanned($username)) {
                echo json_encode(array(
                    "error" => "Your account is banned"
                ));
                return;
            }
        }
        $this->run($username);
        if ($this->callWasSuccessful) {
            $success = array(
                "message" => $this->successMessage,
            );
            $success = array_merge($success, $this->additionalSuccessData);
            echo json_encode(array(
                "success" => $success,
                "apiUsername" => $username
            ));
        } else {
            echo json_encode(array(
                "error" => $this->errorMessage,
                "apiUsername" => $username
            ));
        }
    }

    protected function markCallAsSuccessful($message, $additionalData=array()) {
        $this->successMessage = $message;
        $this->callWasSuccessful = true;
        $this->additionalSuccessData = $additionalData;
    }

    protected function markCallAsFailure($message) {
        $this->errorMessage = $message;
        $this->callWasSuccessful = false;
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
        if (isset($_POST['username']) && isset($_POST['phoneid']) && failsStandardMobileChecksAndEchoFailureMessage()) {
            return null;
        }

        if ($username == null) {
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
