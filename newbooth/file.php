<?PHP

    //Deprecated: Using newbooth/newbooth-scripts

	require_once("{$_SERVER['DOCUMENT_ROOT']}/common/user_utils.php");
	
	require_once("{$_SERVER['DOCUMENT_ROOT']}/newbooth/mobile_utils.php");
	redirectIfMobile();
	
	include("{$_SERVER['DOCUMENT_ROOT']}/content/html.php");
	echo " 
		<link rel='stylesheet' href='/css/file.css' type='text/css' media='screen' />
		<script type = \"text/javascript\" src = \"/newbooth/jpeg_encoder_basic.js\"></script>
		<script type = \"text/javascript\" src = \"/newbooth/jpeg_encoder_threaded.js\"></script>
		<script type = \"text/javascript\" src = \"/newbooth/jpeg_encoder_threaded_worker.js\"></script>
		<script language=\"JavaScript\" src=\"//ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js\"></script>
		<script type = \"text/javascript\" src = \"/newbooth/jquery.FileReader.js\"></script>			
	";
	include("{$_SERVER['DOCUMENT_ROOT']}/content/top.php");
	if (isset($_SESSION['username'])) {	
		
		$username = $_SESSION['username'];
		
		update_online_presence();
		doBanSuspendCheck($username);
			
		$sql = "DELETE FROM 
				`sitemsgtbl` 
				WHERE `fkUsername` = '".$username."' 
				AND `area` = 'capture';";
		mysql_query($sql);
		
		$sql = "SELECT 
				`password` 
				FROM `logintbl` 
				WHERE `username` = '".$username."' 
				LIMIT 1;";
		$row = mysql_fetch_array(mysql_query($sql));
		
		$_SESSION['issuer'] = $row['password'];
		
		printUserNavBar("upload", getDisplayName($username));
	echo "
					<center>
						<a href = '/uploadbooth'>
							<div class = 'subheader' style = 'position: relative; width: 640px;' >
								If you have problems with this page, click here to access the old upload page...
							</div>
						</a>		
						<div id = \"previewspot\" style = \"width: 640px; height: 280px; border: 1px dashed black; box-shadow: 0 0 5px #AAAAAA;\"></div>
						<div style = \"width: 80%;\">
							<form id = \"uploadform\" method = null>
								<div id = \"fileselectsection\" style = \"position: absolute; opacity: 0; z-index: -1; width: 0px; height: 0px;\">
									<input type=\"file\" name=\"file\" id=\"file\" onChange = \"showPreviewAuto(this.files, false)\" />
								</div>
								<div id = \"buttonspot\">
								</div>
								<div class = \"greensection\">
								<input type = \"radio\" name = \"rotation\" id = \"0\" value = 0 checked />
									<label class = \"selected\" for=\"0\"><h1>0&deg;</h1></label>
								<input type = \"radio\" name = \"rotation\" id = \"90\"  value = 90 />
									<label for=\"90\"><h1>90&deg;</h1></label>
								<input type = \"radio\" name = \"rotation\" id = \"180\"  value = 180 />
									<label for=\"180\"><h1>180&deg;</h1></label>
								<input type = \"radio\" name = \"rotation\" id = \"270\"  value = 270 />
									<label for=\"270\"><h1>270&deg;</h1></label>
								</div>
								<textarea style='width: 640px; height: 200px;' id = 'blurb' name='blurb' placeholder='Write a blurb to go with this picture'></textarea><br />
								<div class = 'bigbutton' id = 'submit_button'>Upload Booth</div>
								<progress id = \"progress\" max=\"100\" value=\"0\" style = \"width: 100%\">
								</progress>
							</form>
							<div id = 'status' style = 'height: 32px;'></div>
						</div>
					</center>
					<script type = \"text/javascript\" src = \"/newbooth/file.js\"></script>
		";
			

	
	} else {
		go_to_login();
	}
	include("{$_SERVER['DOCUMENT_ROOT']}/content/bottom.php");

