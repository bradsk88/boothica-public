<?PHP

require_once "{$_SERVER['DOCUMENT_ROOT']}/common/db_auth.php";

function sql_query($sql) {
    $dblink = connect_boothDB();
    $query = $dblink->query($sql);
    if (false===$query) {
        sql_death1($sql);
        return null;
    }

    return $query;
}

function sql_get_expectOneRow($query, $column) {
    if (emptyResult($query)) {
        return null;
    }
    $row = $query->fetch_assoc();
    return $row[$column];
}

function emptyResult($query) {
    return $query->num_rows == 0;
}

function sql_death1($sql) {
    echo "death with SQL".$sql; //TODO: Remove this
    return;
    $usern = "not logged in";
    if (isset($_SESSION['usernum'])) {
        $usern = $_SESSION['usernum'];
    }
    if (isset($_SESSION['username'])) {
        $usern = $_SESSION['username'];
    }
    ob_start();
    debug_print_backtrace();
    $trace = ob_get_clean();
    //TODO: Enable on prod
//    foreach (getDevs() as $dev) {
//        error_log("You are receiving this because you are on the developers list\n\n"."MySQL Death\nUsername at time of death: "
//            .$usern."\nRequest page: ".$_SERVER['REQUEST_URI']."\nScript page: ".__FILE__.": \n" . (isset($dblink) ? mysqli_error($dblink) : "N/A")."\n\n".$sql.get_ip_address()."\n\n".$trace, 1, $dev);
//    }
    return "Database error.";
}