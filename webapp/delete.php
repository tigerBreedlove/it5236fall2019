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
	$dbconnecterror = TRUE;
}
if ($_SERVER['REQUEST_METHOD'] == "POST") {
	if (!$dbconnecterror) {
		try {
			$sql = "DELETE FROM doList where listID = :listID";
			$stmt = $dbh->prepare($sql);			
			$stmt->bindParam(":listID", $_POST['listID']);
		
			$response = $stmt->execute();	
			
			header("Location: index.php");
			
		} catch (PDOException $e) {
			header("Location: index.php?error=delete");
		}	
	} else {
		header("Location: index.php?error=delete");
	}
}
?>
