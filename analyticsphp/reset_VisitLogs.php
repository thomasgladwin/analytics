<?php
$fileloc = "open_conn.php";
require($fileloc);

$reset_pw = $_GET["password"];
if ($reset_pw != $reset_page_password) {
	return;
}

try {	
	$sql = "use ".$db_name;
	$result = $conn->query($sql);
} catch(PDOException $e) {
	echo $sql . "<br>" . $e->getMessage();
}

try {
	$sql = "DROP TABLE IF EXISTS VisitLogs";
	$result = $conn->query($sql);
} catch(PDOException $e) {
	echo $sql . "<br>" . $e->getMessage();
}
try {
	$sql = "CREATE TABLE IF NOT EXISTS VisitLogs (
	id INT(6) AUTO_INCREMENT PRIMARY KEY,
	visit_id VARCHAR(255),
	current VARCHAR(255),
	target VARCHAR(255),
	log_time DATETIME,
	ip VARCHAR(255),
	timeOnPage BIGINT
	)";
	$result = $conn->query($sql);
} catch(PDOException $e) {
	echo $sql . "<br>" . $e->getMessage();
}

try {
	$this_datetime = date('Y-m-d H:i:s');
	$sql = "INSERT INTO LoggingInfo (last_restart) VALUES ('".$this_datetime."')";
	$result = $conn->query($sql);
} catch(PDOException $e) {
	echo $sql . "<br>" . $e->getMessage();
}

echo '<p>Show tables:<br>';
try {	
	$sql = "show tables";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
	  while($row = $result->fetch_assoc()) {
		foreach ($row as $value) {
			echo $value."<br>";
		}
	  }
	} else {
	  echo "0 results<br>";
	}
} catch(PDOException $e) {
	echo $sql . "<br>" . $e->getMessage();
}

$conn->close();

?> 
