<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Declare the credentials to the database
$dbconnecterror = FALSE;
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
//UPDATE - PUT
if ($_SERVER['REQUEST_METHOD'] == "PUT") {
	if(array_key_exists('listID',$_GET)){
		$listID = $_GET['listID'];
	}else{
		http_response_code(400);
		echo "Missing listID";
		exit(); 
	}
	$listID = $_GET['listID'];
	//decode the json body from the request
	$task = json_decode(file_get_contents('php://input'), true);
	if (array_key_exists('completed', $task)) {
		$complete = $task["completed"] ? 1 : 0;
	} else {
		http_response_code(400);
		echo "Missing completed status";
		exit();
	}
	if (array_key_exists('taskName', $task)) {
		$taskName = $task["taskName"];
	} else {
		http_response_code(400);
		echo "Missing taskName";
		exit();
	}
	if (array_key_exists('taskDate', $task)) {
		$taskDate = $task["taskDate"];
	} else {
		http_response_code(400);
		echo "Missing taskDate";
		exit();
	}
	if (!$dbconnecterror) {
		try {
			$sql = "UPDATE doList SET complete=:complete, listItem=:listItem, finishDate=:finishDate WHERE listID=:listID";
			$stmt = $dbh->prepare($sql);
			$stmt->bindParam(":complete", $complete);
			$stmt->bindParam(":listItem", $taskName);
			$stmt->bindParam(":finishDate", $taskDate);
			$stmt->bindParam(":listID", $listID);
			$response = $stmt->execute();
			http_response_code(204);
			
			exit();
		} catch (PDOException $e) {
			http_response_code(504);
			echo "Database exception";
			exit();
		}
	} else {
		http_response_code(504);
	}
//DELETE
} else if ($_SERVER['REQUEST_METHOD'] == "DELETE") {
	if(array_key_exists('listID',$_GET)){
		$listID = $_GET['listID'];
	}else{
		http_response_code(400);
		echo "Missing listID";
		exit(); 
	}
	$listID = $_GET['listID'];
	if (!$dbconnecterror) {
		
		try {
			$sql = "DELETE FROM doList WHERE listID=:listID";
			$stmt = $dbh->prepare($sql);
			$stmt->bindParam(":listID", $listID);
			$response = $stmt->execute();
			http_response_code(204);
			
			exit();
		} catch (PDOException $e) {
			http_response_code(504);
			echo "Database exception";
			exit();
		}
	} else {
		http_response_code(504);
	}
//READ - GET
} else if ($_SERVER['REQUEST_METHOD'] == "GET") {
	if(array_key_exists('listID',$_GET)){
		$listID = $_GET['listID'];
	}else{
		http_response_code(400);
		echo "Missing listID";
		exit(); 
	}
	$listID = $_GET['listID'];
	if (!$dbconnecterror) {
		
		try {
			$sql = "SELECT * FROM doList WHERE listID=:listID";
			$stmt = $dbh->prepare($sql);
			$stmt->bindParam(":listID", $listID);
			$response = $stmt->execute();
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			if (!is_array($result)){
				http_response_code(404);
				exit();
			}
			
			http_response_code(200);
			echo json_encode ($result);
			exit();
		} catch (PDOException $e) {
			http_response_code(504);
			echo "Database exception";
			exit();
		}
	} else {
		http_response_code(504);
	}
	
//CREATE - POST	
} else if ($_SERVER['REQUEST_METHOD'] == "POST") {
	$task = json_decode(file_get_contents('php://input'), true);
	if (array_key_exists('completed', $task)) {
		$complete = $task["completed"] ? 1 : 0;
	} else {
		http_response_code(400);
		echo "Missing completed status";
		exit();
	}
	if (array_key_exists('taskName', $task)) {
		$taskName = $task["taskName"];
	} else {
		http_response_code(400);
		echo "Missing taskName";
		exit();
	}
	if (array_key_exists('taskDate', $task)) {
		$taskDate = $task["taskDate"];
	} else {
		http_response_code(400);
		echo "Missing taskDate";
		exit();
	}
	if (!$dbconnecterror) {
		try {
			$sql = "INSERT INTO doList (complete, listItem, finishDate) values(:complete,:listItem,:finishDate)";
			$stmt = $dbh->prepare($sql);
			$stmt->bindParam(":complete", $complete);
			$stmt->bindParam(":listItem", $taskName);
			$stmt->bindParam(":finishDate", $taskDate);
			$response = $stmt->execute();
			$taskID = $dbh->lastInsertId();
				$fulltask = [
				"listID"=>$taskID,
				"complete"=>$complete,
				"listItem"=>$taskName,
				"finishDate"=>$taskDate
				];
			http_response_code(201);
			echo json_encode($fulltask);
			exit();
		} catch (PDOException $e) {
			http_response_code(504);
			echo "Database exception";
			exit();
		}
	} else {
		http_response_code(504);
	}
	
} else {
	http_response_code(405);//method not allowed
	echo "Unsupported HTTP method";
	exit();
}
