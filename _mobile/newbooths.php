<?php
error_reporting(0);

function get_ip_address() {
	foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
		if (array_key_exists($key, $_SERVER) === true) {
			foreach (explode(',', $_SERVER[$key]) as $ip) {
				if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
					return $ip;
				}
			}
		}
	}
}

$link = mysql_connect('boothsite.affirm.me','boothroot','2ndDailybooth'); 
if (!$link) { 
	die('Could not connect to MySQL: ' . mysql_error()); 
} 

$db = mysql_select_db('boothsite', $link); 
if (!$db) { 
	die('Could not connect to db: ' . mysql_error()); 
}



  
$return_arr = array();

$sql = "SELECT `imageTitle`, `blurb`, `fkUsername` FROM `boothnumbers` WHERE `fkUsername` IN (SELECT `fkUsername` FROM `userspublictbl`) ORDER BY `pkNumber` DESC LIMIT 5;";
$result = mysql_query($sql);

while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
    $row_array['imageTitle'] = $row['imageTitle'];
    $row_array['blurb'] = $row['blurb'];
    $row_array['fkUsername'] = $row['fkUsername'];

    array_push($return_arr,$row_array);
}

echo json_encode($return_arr);		

?> 