<?PHP
	include("{$_SERVER['DOCUMENT_ROOT']}/html_top.php");
	echo "
		<link rel='stylesheet' href='/css/file.css' type='text/css' media='screen' />
		<script class=\"jsbin\" src=\"http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.js\"></script>
		<script language=\"text/javascript\" src = \"http://code.jquery.com/ui/1.10.0/jquery-ui.js\"></script>
		<script type = \"text/javascript\" src = \"/newbooth/megapix-image.js\"></script>	
		<script type = \"text/javascript\" src = \"/newbooth/file.js\"></script>
		<script type = \"text/javascript\" src = \"/newbooth/jpeg_encoder_basic.js\"></script>
		<script type = \"text/javascript\" src = \"/newbooth/jpeg_encoder_threaded.js\"></script>
		<script type = \"text/javascript\" src = \"/newbooth/jpeg_encoder_threaded_worker.js\"></script>
		<script language=\"JavaScript\" src=\"//ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js\"></script>
		<script type = \"text/javascript\" src = \"/newbooth/jquery.FileReader.js\"></script>	
		<meta name=\"viewport\" content=\"width=device-width; initial-scale = 1.0; maximum-scale=1.0; user-scalable=no\" />
		<style>
			body {
				margin: 0;
			}
		</style>
	</head>
	<body>
	";
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
		
	echo "
					<center>
						<a href = '/uploadbooth'>
							<div class = 'subheader' style = 'position: relative; width: 100%;' >
								This page is still being tested.  If you have troubles, click here to use the old one
							</div>
						</a>
	";
	
		echo "	
		
								<div id = \"previewspot\" style = \"width: 100%; height: 20px; border: 1px dashed black; box-shadow: 0 0 5px #AAAAAA;\"></div>
								<div style = \"width: 100%;\">
									<form id = \"uploadform\">
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
										<div id = \"fileselectsection\" style = \"position: absolute; opacity: 0; z-index: -1; width: 0px; height: 0px;\">
											<input type=\"file\" name=\"file\" id=\"file\" onchange=\"showPreview(this.files, true)\"/>
										</div>
										<h1><div id = \"buttonspot\">
										</div></h1>
										<textarea style='width: 100%; height: 200px;' id = 'blurb' name='blurb' placeholder='Write a blurb to go with this picture'></textarea><br />
										<h1><div class = 'bigbutton' id = 'submit_button'>Upload Booth</div></h1>
									</form>
									<progress id = \"progress\" max=\"100\" value=\"0\" style = \"width: 100%\">
									</progress>
									<div id = 'status' style = 'height: 0px'></div>
								</div>
						</center>
		";
			

	
	} else {
		go_to_login();
	}
	echo "
		</body>
	</html>
	";

