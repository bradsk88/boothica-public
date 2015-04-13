<?PHP

require_once "{$_SERVER['DOCUMENT_ROOT']}/common/utils.php";
include("{$_SERVER['DOCUMENT_ROOT']}/content/html.php");
echo "
	<link rel = 'stylesheet' href = '/css/account.css'  type='text/css' media='screen' />
";
include("{$_SERVER['DOCUMENT_ROOT']}/common/smallpage_top.php");
include("{$_SERVER['DOCUMENT_ROOT']}/common/header.php");
main();
echo "
    </body>
</html>
";

function main() {

    if (!isset($_SESSION['username'])){
        go_to_login();
        return;
    }

    $username = $_SESSION['username'];

    $carried = "";
    if (isset($_GET['value'])) {
        $carried = $_GET['value'];
    }

    if (hasNoEmail($username))  {
        echo "
    <div class = \"account-problem\">
        Your account needs an email address.  If you do not have an email address you will not recieve notifications from the site and you will not be able to regain access to your account if you forget your password.
    </div>";
    }

    echo "
    <form id = \"emailform\" action = \"/actions/changeemail\" method = \"post\">
    <div class = 'setting'>
        <stitle>Change Email</stitle><br/>
        <div class = \"setting-desc\">
            <div class = \"setting-text\">
                New Email Address:
            </div>
            <div style = \"float: left;\">
                <input type = \"text\" name = \"newemail\" id = \"newemail\" value = \"".$carried."\" style = \"width: 300px;\" />
            </div>
            <div style = \"clear: both;\"></div>
            <br/>
            <div class = \"setting-text\">
                Confirm New Email:
            </div>
            <div style = \"float: left;\">
                <input type = \"text\" name = \"newemailconf\" id = \"newemailconf\"  style = \"width: 300px\" />
            </div>
            <div style = \"clear: both;\"></div>
        </div>
        <div class = \"setting-cur\">
    ";
    if (isset($_GET['nomatch'])) {
        echo "<span style = \"color: red;\">Emails did not match </span>";
    } else if (isset($_GET['notvalid'])) {
        echo "<span style = \"color: red;\">You must enter a valid email address </span>";
    }
    echo "
            <span id = \"submit\">
                <img src= \"/media/edit.png\"> Submit Changes
            </span>
        </div>
    </div>
    </form>
    <script type = \"text/javascript\">
        $('#submit').one('click', function() {
            $('#emailform').submit();
        });
    </script>
    ";

}
