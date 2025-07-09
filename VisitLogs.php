 <?php
try {
	require("open_conn.php");
	
	$pw = $_GET["password"];
	if ($pw != $password_get) {
		return;
	}
	
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
	
	$this_datetime = date('Y-m-d H:i:s');
	session_start();
	$this_ip = $_SERVER['REMOTE_ADDR'];

	try {	
		$sql = "INSERT INTO VisitLogs (visit_id, current, target, log_time, ip) VALUES (?, ?, ?, ?, ?)";
		$stmt = $conn->prepare($sql);
		$stmt->bind_param("sssss", $visit_id, $current, $target, $this_datetime, $this_ip);
		$stmt->execute();
	} catch(PDOException $e) {
		echo $e;
	}
	$conn->close();

} catch (Exception $e) {
	echo $e;
}

?> 
