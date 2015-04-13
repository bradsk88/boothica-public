<?PHP

require_once "{$_SERVER['DOCUMENT_ROOT']}/common/reg_utils.php";
include "{$_SERVER['DOCUMENT_ROOT']}/content/html.php";
echo "
	<link rel = 'stylesheet' href = '/css/account.css'  type='text/css' media='screen' />
";
include "{$_SERVER['DOCUMENT_ROOT']}/common/top.php";
main();

function main() {

    if (!isset($_SESSION['username'])) {
        go_to_login();
        return;
    }

    $username = $_SESSION['username'];

    if (hasNoSecurity($username))  {
        echo "
    <div class = \"account-problem\">
        Your security setting has not been selected properly.  <br/>You are currently defaulting to <b>Super Security</b> which means you cannot use the \"Forgot my password\" function.
    </div>";
    }

    echo "
    <form id = \"emailform\" action = \"/actions/changeemail\" method = \"POST\">
    <div class = 'setting'>
        <stitle>Change Security Setting</stitle><br/>
        <div class = \"setting-desc\">
            <div class = \"setting-text\">
                 With Normal Security:
            </div><br/>
            <div style = 'padding: 10px;'>
    ";
    printNormalSecurityDescription();
    echo "
                <div class = \"setting-cur\">
                    <a href = \"/actions/changesecurity?mode=normal\">Use this setting</a>
                </div>
            </div>
        </div>

        <div class = \"setting-desc\">
            <div class = \"setting-text\">
                 With High Security:
            </div><br/>
            <div style = 'padding: 10px;'>
    ";
    printHighSecurityDescription();
    echo "
                <div class = \"setting-cur\">
                    <a href = \"/actions/changesecurity?mode=secure\">Use this setting</a>
                </div>
            </div>
        </div>

        <div class = \"setting-desc\">
            <div class = \"setting-text\">
                 With Super Security:
            </div><br/>
            <div style = 'padding: 10px;'>
    ";
    printVeryHighSecurityDescription();
    echo "
                <div class = \"setting-cur\">
                    <a href = \"/actions/changesecurity?mode=super\">Use this setting</a>
                </div>
            </div>
        </div>
    </div>
    </form>
    </div>
    </body>
    </html>
    ";

}
