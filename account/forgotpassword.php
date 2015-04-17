<?PHP

require_once "{$_SERVER['DOCUMENT_ROOT']}/framing/PageFrame.php";

main();

function main() {

    $html = null;

    if (isset($_REQUEST['un']) && strlen($_REQUEST['un']) > 0) {
        if (isset($_REQUEST['mail'])) {
            doRecover($_REQUEST['un'], $_REQUEST['mail']);
            return;
        }

        $htmlBuilder = new h2o("{$_SERVER['DOCUMENT_ROOT']}/account/templates/forgotEnterEmail.mst");
        $html = $htmlBuilder->render(array(
            "baseUrl" => base(),
            "username" => $_REQUEST['un']
        ));
        $page = buildPage($html);
        $page->echoHtml();
        return;
    }

    $htmlBuilder = new h2o("{$_SERVER['DOCUMENT_ROOT']}/account/templates/forgotEnterUsername.mst");
    $html = $htmlBuilder->render(array(
        "baseUrl" => base()
    ));
    $page = buildPage($html);
    $page->echoHtml();
    return;
}

function doRecover($username, $email) {
    $dblink = connect_boothDB();
    $sqlBuilder = new h2o("{$_SERVER['DOCUMENT_ROOT']}/account/queries/getEmail.mst.sql");
    $sql = $sqlBuilder->render(array(
        "username" => $username,
        "email" => $email
    ));
    $mailres = $dblink->query($sql);
    if (!$mailres) {
        sql_death1($sql);
        return;
    }

    $num = $mailres->num_rows;
    if ($num == 0) {
        $htmlBuilder = new h2o("{$_SERVER['DOCUMENT_ROOT']}/account/templates/forgotEmailMismatch.mst");
        $html = $htmlBuilder->render(array(
            "baseUrl" => base()
        ));
        $page = buildPage($html);
        $page->echoHtml();
        return;
    }

    require_once "{$_SERVER['DOCUMENT_ROOT']}/common/internal_utils.php";
    $newpass = generateRandomString();

    $changed = changePassword($username, $newpass);
    if ($changed) {
        $user = getDisplayName($username);
        $site = base();
        $quotefixer = mt_rand(5, 15);
        $msg = "<span id = '$quotefixer'></span>Your temporary password is <br/><h3>$newpass</h3><br/>\n";
        $quotefixer = mt_rand(5, 15);
        $msg .= "<span id = '$quotefixer'></span><a href = 'http://$site/login'>Log in to Boothi.ca</a> using this password and change it via the <img src = 'http://$site/media/settings.png'> Account page.";
        sendBoothicaEmail($email, $user." : Password Reset", $msg);

        $htmlBuilder = new h2o("{$_SERVER['DOCUMENT_ROOT']}/account/templates/forgotResetSuccess.mst");
        $html = $htmlBuilder->render(array(
            "baseUrl" => base(),
            "email" => $email
        ));
        $page = buildPage($html);
        $page->echoHtml();
        return;
    }
    $page = buildPage("Error");
    $page->echoHtml();
    return;
}

function buildPage($html) {
    $page = new PageFrame();
    $page->excludeLoginNotification();
    $page->body($html);
    $page->css(base()."/css/forgotpassword.css");
    $page->css("http://fonts.googleapis.com/css?family=Bitter:400,700");
    return $page;
}