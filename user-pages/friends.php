<?PHP

    require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";
    require_once "{$_SERVER['DOCUMENT_ROOT']}/pages/ErrorPage.php";
    require_once "{$_SERVER['DOCUMENT_ROOT']}/framing/PageFrame.php";
    require_common("utils");
    require_asset("UserImage");

    if (!isset($_REQUEST['username'])) {
        $page = new ErrorPage("Missing parameter: username");
        $page->echoHtml();
        return;
    }
    $boothername = $_REQUEST['username'];

    $possessiveName = getPossessiveDisplayName($boothername);

    $page = new PageFrame();
    $page->setBodyTemplateAndValues(
        "{$_SERVER['DOCUMENT_ROOT']}/user-pages/templates/userFriendsFrame.mst",
        array(
            "isLoggedIn" => isLoggedIn(),
            "userPossessiveDisplayName" => $possessiveName,
            "userImageUrl" => UserImage::getAbsoluteImage($boothername),
            "username" => getDisplayName($boothername)
        )
    );
    $page->useDefaultSideBars();
    $page->css(base()."/css/friends.css");
    $page->script(base()."/user-pages/friends-scripts.js");
    $page->rawScript("<script type = \"text/javascript\">
        loadFriendsList('".$boothername."');
    </script>");
    $page->echoHtml();
