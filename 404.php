<?PHP

main();

function main() {

	$urlparts = explode( '/', $_SERVER['REQUEST_URI'] );	
	$dirname = $urlparts[2];

	if ($dirname == '404') {
		include("{$_SERVER['DOCUMENT_ROOT']}/errors/404page.php");
		return;
	} 

	include("{$_SERVER['DOCUMENT_ROOT']}/content/html.php");
	include "{$_SERVER['DOCUMENT_ROOT']}/common/smallpage_top.php";
	include("{$_SERVER['DOCUMENT_ROOT']}/common/header.php");
	
	require_once("{$_SERVER['DOCUMENT_ROOT']}/common/universal_utils.php");
	
	print404();

	echo "
	</body>
</html>
	";
}

