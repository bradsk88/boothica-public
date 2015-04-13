<?PHP

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

    echo "
    <form id = \"emailform\" action = \"/actions/changepassword\" method = \"post\">
    <div class = 'setting'>
        <stitle>Change Password</stitle><br/>
        <div class = \"setting-desc\">
            <div class = \"setting-text\">
                Current Password:
            </div>
            <div style = \"float: left;\">
                <input type = \"password\" name = \"currentpass\" id = \"currentpass\" style = \"width: 300px;\" />
            </div>
            <div style = \"clear: both;\"></div>
            <br/>
            <div class = \"setting-text\">
                New Password:
            </div>
            <div style = \"float: left;\">
                <input type = \"password\" name = \"newpass\" id = \"newpass\" style = \"width: 300px;\" />
            </div>
            <div style = \"clear: both;\"></div>
            <br/>
            <div class = \"setting-text\">
                Confirm Password:
            </div>
            <div style = \"float: left;\">
                <input type = \"password\" name = \"newpassconf\" id = \"newpassconf\"  style = \"width: 300px\" />
            </div>
            <div style = \"clear: both;\"></div>
        </div>
        <div class = \"setting-cur\">
    ";
    if (isset($_GET['nomatch'])) {
        echo "<span style = \"color: red;\">Passwords did not match </span>";
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