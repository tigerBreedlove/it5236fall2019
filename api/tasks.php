<?php
// Declare the credentials to the database
$dbh = NULL;
require_once 'credentials.php';
try{
	$conn_string = "mysql:host=".$dbserver.";dbname=".$db;
	$dbh= new PDO($conn_string, $dbusername, $dbpassword);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}catch(Exception $e){
	//database issues were encountered
	http_response_code(504);
	echo "Databsae timeout";
	exit();
}
//READ - GET
if ($_SERVER['REQUEST_METHOD'] == "GET") {
	$listID = $_GET['listID'];
		
	try {
		$sql = "SELECT listID, listItem AS taskName, finishDate AS taskDate, complete AS completed FROM doList";
		$stmt = $dbh->prepare($sql);
		$response = $stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		$final = [];
		foreach($result as $task) {
			$task['completed'] = $task['completed'] ? true : false;
			$final[] = $task;
		}
		http_response_code(200);
		echo json_encode($final);
		exit();
	} catch (PDOException $e) {
		http_response_code(504);
		echo "Database exception";
		exit();
		}
} else {
	http_response_code(405);//method not allowed
	echo "Unsupported HTTP method";
	exit();
}
