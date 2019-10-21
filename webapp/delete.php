<?php
//sets the configuration option value
ini_set('display_errors', 1);
//sets the configuration option value
ini_set('display_startup_errors', 1);
//reports all errors
error_reporting(E_ALL);
// Declare the credentials to the database
$dbconnecterror = FALSE;
$dbh = NULL;
//requires the file to be presented once
require_once 'credentials.php';
try{
	//establishes a database connection
	$conn_string = "mysql:host=".$dbserver.";dbname=".$db;
	
	//uses specific credentials
	$dbh= new PDO($conn_string, $dbusername, $dbpassword);
	//throws exceptions for error reporting
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	//will execute if the exception is not caught in previous steps
}catch(Exception $e){
	//sets the connection to TRUE
	$dbconnecterror = TRUE;
}
//executes if posting
if ($_SERVER['REQUEST_METHOD'] == "POST") {
	if (!$dbconnecterror) {
		try {
			//deletes an item
			$sql = "DELETE FROM doList where listID = :listID";
			//executes the prepared sql statement
			$stmt = $dbh->prepare($sql);		
			//binds the value for the sql statment
			$stmt->bindParam(":listID", $_POST['listID']);
		
			//executes the statement
			$response = $stmt->execute();	
			//sends a raw http header
			header("Location: index.php");
			//if there is a PDOException sends a raw http header
		} catch (PDOException $e) {
			header("Location: index.php?error=delete");
		}	
	//if it workds, sends this header
	} else {
		header("Location: index.php?error=delete");
	}
}
?>
