<?PHP

    require_once "{$_SERVER['DOCUMENT_ROOT']}/modpages/newspost.php";
    $newsPost = new NewsPost();
    $newsPost->show();
