<?php
		session_start();
		//echo 'Sess ID in VL: '.session_id();
		//var_dump($_SESSION['php_access_codes']);
		//echo '<script>console.log("REMOTE_ADDR: '.$_SERVER['REMOTE_ADDR'].'");</script>';
		//echo "REMOTE_ADDR: ".$_SERVER['REMOTE_ADDR']." - ";
		//echo "SERVER_SERVER_ADDR: ".$_SESSION['SERVER_SERVER_ADDR']." - ";
?>
 
<?php
try {
	//session_start();
	if (!isset($_SESSION['php_access_codes'])) {
		die('No access: 1.');
	}

	require("open_conn.php");
	
	try {	
		$sql = "use ".$db_name;
		$result = $conn->query($sql);
	} catch(PDOException $e) {
	}
	
	try {
		$visit_id = $_GET["vid"];
	} catch(Exception $e) {
		$visit_id = "0";
	}
	try {
		$current = strtok($_GET["current"], '?');
	} catch(Exception $e) {
		$current = "0";
	}
	try {
		$target = strtok($_GET["target"], '?');
	} catch(Exception $e) {
		$target = "0";
	}
	try {
		$timeOnPage = strtok($_GET["timeOnPage"], '?');
	} catch(Exception $e) {
		$timeOnPage = "0";
	}
	
	$this_datetime = date('Y-m-d H:i:s');
	session_start();
	$this_ip = $_SERVER['REMOTE_ADDR'];

	try {	
		$sql = "INSERT INTO VisitLogs (visit_id, current, target, log_time, ip, timeOnPage) VALUES (?, ?, ?, ?, ?, ?)";
		$stmt = $conn->prepare($sql);
		$stmt->bind_param("ssssss", $visit_id, $current, $target, $this_datetime, $this_ip, $timeOnPage);
		$stmt->execute();
	} catch(PDOException $e) {
		echo $e;
	}
	$conn->close();

} catch (Exception $e) {
	echo $e;
}

?> 
