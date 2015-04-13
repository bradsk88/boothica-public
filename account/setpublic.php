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

    if(!isset($_GET['private'])) {
        print404();
        return;
    }

    echo "
    <form id = \"visform\" action = \"/actions/setpublic\" method = \"post\">
    <div class = 'setting'>
        <stitle>Change Visibility</stitle><br/>
        <div class = \"setting-desc\">
            Your password is required to change your account visibility.
            <div class = \"setting-text\">
                Password:
            </div>
            <div style = \"float: left;\">
                <input type = \"password\" name = \"password\" id = \"password\" style = \"width: 200px;\" />
            </div>
            <div style = \"clear: both;\"></div>
            <br/>
        </div>
        <div class = \"setting-cur\">
    ";
    if (isset($_GET['nomatch'])) {
        echo "<span style = \"color: red;\">Passwords did not match </span>";
    }

    echo "
            <span id = \"submit\">
                <img src= \"/media/edit.png\">
    ";
    if ($_GET['private'] == 'true' ) {
        echo "
            <input type = \"hidden\" value = \"true\" name = \"public\"/>
            Click to go public";
    } else {
        echo "
            <input type = \"hidden\" value = \"false\" name = \"public\"/>
            Click to go private";
    }

    echo "
            </span>
        </div>
    </div>
    </form>
    <script type = \"text/javascript\">
        $('#submit').one('click', function() {
            $('#visform').submit();
        });
    </script>
    ";

}