<?php
if ($_SERVER['REQUEST_METHOD'] == "POST") {
	
	$listID = $_POST['listID'];
	
	//build url for api
	$url = "http://3.213.65.50/api/task.php?listID=$listID";

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$response  = curl_exec($ch); //body of the response
	$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	
	//status code 204
	if($httpcode==203) {
		header("Location: index.php");
	} else {
		header("Location: index.php?error=edit");
	}
}

