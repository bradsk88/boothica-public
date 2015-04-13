<?PHP

require_once "{$_SERVER['DOCUMENT_ROOT']}/common/reg_utils.php";
include "{$_SERVER['DOCUMENT_ROOT']}/content/html.php";
echo "
	<link rel = 'stylesheet' href = '/css/account.css'  type='text/css' media='screen' />
";
include "{$_SERVER['DOCUMENT_ROOT']}/common/top.php";
main();

function main() {

    echo "
        <div class = 'setting'>
            <stitle>Change Feed Page Layout</stitle><br/>
            <div class = \"setting-desc\">
                <div class = \"setting-text\">
                     All Activity:
                </div><br/>
                <div style = 'padding: 10px;'>
                    ".getAllActivityDescription()."
                    <div class = \"setting-cur\">
                        <a href = \"/actions/changelayout?mode=all\">Use this setting</a>
                    </div>
                </div>
            </div>

            <div class = \"setting-desc\">
                <div class = \"setting-text\">
                     Booths Only:
                </div><br/>
                <div style = 'padding: 10px;'>
                    ".getBoothsOnlyDescription()."
                    <div class = \"setting-cur\">
                        <a href = \"/actions/changelayout?mode=booths\">Use this setting</a>
                    </div>
                </div>
            </div>

            <div class = \"setting-desc\">
                <div class = \"setting-text\">
                     Today At a Glance:
                </div><br/>
                <div style = 'padding: 10px;'>
                    ".getTodayDescription()."
                    <div class = \"setting-cur\">
                        <a href = \"/actions/changelayout?mode=today\">Use this setting</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </body>
    </html>
    ";

}
