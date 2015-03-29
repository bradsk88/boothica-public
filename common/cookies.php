<?PHP

function cookie_set() {

if (!isset($_COOKIE['userid'])) {
    return -1;
}

$parts = explode(":", $_COOKIE['userid']);
	$name = $parts[0];
	$rando = $parts[1];

	$sql = "SELECT 
			`cookie` 
			FROM `remembertbl` 
			WHERE `fkUsername` = '".$name."' 
			AND `cookie` = '".$rando."' 
			AND `expiredate` > NOW();";
	$result = mysql_query($sql);

	if (mysql_num_rows($result) >= 1) {
	
		$sql = "DELETE FROM
				`remembertbl`
				WHERE `cookie` = '".$rando."'
				OR `expiredate` < NOW();";

		$delres = mysql_query($sql);
		if (!$delres) {
			mysql_death1($sql);
		}
	
		$_SESSION['username'] = $name;
	
		//change cookie to something else.
		
		//get real random string start

		//linux?
		$fp = @fopen('/dev/urandom','rb');
		$pr_bits = "";
		if ($fp !== FALSE) {
			$pr_bits .= @fread($fp,16);
			@fclose($fp);
		}
		
		//end get real random string

		//turn to string that can be stored.
		$pr_bits = base64_encode($pr_bits);
		//remove nasty chars
		$pr_bits = str_replace(array(":", "'", '"', '=', "+", "/"), "", $pr_bits);
		
		//set cookie with username and random bits
		$exp = time()+60*60*24*30;
		$set = setcookie("userid", $name . ":" . $pr_bits, $exp, "/");
		if (!$set) {
			death("setcookie failed for name: ".$name."\npr_bits: ".$pr_bits."\nexp: ".$exp);
		}

		$sql = "INSERT INTO 
				`remembertbl`
				(`cookie`, `fkUsername`, `expiredate`)
				VALUES
				('".$pr_bits."', '".strtolower($name)."', NOW() + INTERVAL 30 DAY);";
		$insertres = mysql_query($sql);
		if (!$insertres) {
			mysql_deathm($sql, "failed insert on cookie");
		}
								
		$parts = null;
		$name = null;
		$rando = null;
		return 0;
	
	} else {
		return -1;
	}
}