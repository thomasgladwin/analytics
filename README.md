# Homebrew analytics

This is a basic method to log basic site-visitor behaviour: which pages are visited, and which links are clicked. The logging involves (1) setting up the database (the database-hosting assumed to have been done; I used Dreamhost's MySQL hosting), (2) JavaScript (with some PHP) to be placed in the header of each page to be logged, (3) a PHP file called from the JavaScript, and (4) a PHP file to display the database.

Scripts are works-in-progress, obviously no guarantees.

## open_conn.php: Open database connection, reused in multiple functions below
```
<?php
$servername = "mysql.xxx.com";
$username = "xxx";
$password = "xxx";
$db_name = "xxx";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

?> 
```

## Database setup
```
<?php
require("open_conn.php");
$page_password = 'fgPLPhfdhg63245cdfd';

$reset_pw = $_GET["password"];
if ($reset_pw != $page_password) {
	return;
}

try {	
	$sql = "use tegladwin_db";
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
	log_time DATETIME
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

```
## Script for calling the logging function on events
```
	<script>
		<?php
			if (!isset($_SESSION['VisitID'])) {
				$_SESSION['VisitID'] = 'VID'.strval(rand());
			}
			$vid = $_SESSION['VisitID'];
			echo "let vid = '$vid';";
		?>
		
		document.addEventListener('mousedown', (event) => myFunctionLinkClick(event));
		
		document.addEventListener('DOMContentLoaded', function() {
			//window.alert("Testing 2 ");
			var xhttpOnload = new XMLHttpRequest();
			xhttpOnload.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200) {
					//document.getElementById("demo").innerHTML = xhttp.responseText;
					//window.alert("Testing " + xhttp.responseText);
				}
			}
			arg0 = "/VisitLogs.php?vid=" + vid + "&current=" + location.pathname + "&target=0&anticache=" + Math.random() + "&password=xxx";
			xhttpOnload.open("GET", arg0, true);
			xhttpOnload.send();
		}, false);

		let myFunctionLinkClick = function(event) {
			let t = event.target;
			if (typeof t.href == "string") {
				var xhttp = new XMLHttpRequest();
				var xhttp = new XMLHttpRequest();
				xhttp.onreadystatechange = function() {
					if (this.readyState == 4 && this.status == 200) {
						//document.getElementById("demo").innerHTML = xhttp.responseText;
						//window.alert("Testing " + xhttp.responseText);
					}
				}
				arg0 = "/VisitLogs.php?vid=" + vid + "&current=" + location.pathname + "&target=" + t.href + "&anticache=" + Math.random() + "&password=xxx";
				xhttp.open("GET", arg0, true);
				xhttp.send();
				//window.alert("Testing 2 ");
			}
		}

		//window.alert("Testing " + vid);
		
	</script>

```
## VisitLogs.php: PHP file for logging
```
 <?php
try {
	require("open_conn.php");
	$password_get = "xxx";

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

	try {	
		$sql = "INSERT INTO VisitLogs (visit_id, current, target, log_time) VALUES (?, ?, ?, ?)";
		$stmt = $conn->prepare($sql);
		$stmt->bind_param("ssss", $visit_id, $current, $target, $this_datetime);
		$stmt->execute();
	} catch(PDOException $e) {
		echo $e;
	}
	$conn->close();

} catch (Exception $e) {
	echo $e;
}

?> 

```
## PHP file for display
```
<?php
require("open_conn.php");

try {	
	$sql = "use ".$db_name;
	$result = $conn->query($sql);
} catch(PDOException $e) {
	echo $sql . "<br>" . $e->getMessage();
}

echo '<p>Database reset at: ';
try {
	$stmt = $conn->prepare("select last_restart from LoggingInfo");
	$stmt->execute();
	$result = $stmt->get_result();
	if ($result->num_rows > 0) {
	 while($row = $result->fetch_assoc()) {
		echo $row["last_restart"];
	  }
	} else {
	  echo "No results<br>";
	}
} catch(PDOException $e) {
	echo $sql . "<br>" . $e->getMessage();
}

echo '<p>Page visit counter (unique per visit):<br>';
try {
	$stmt = $conn->prepare('WITH CTE1 AS (select distinct visit_id, current from VisitLogs where target = "0" order by log_time desc) Select current, COUNT(*) as N FROM CTE1 GROUP BY CURRENT;');
	$stmt->execute();
	$result = $stmt->get_result();
	if ($result->num_rows > 0) {
	 echo '<table>';
	 while($row = $result->fetch_assoc()) {
		echo '<tr>';
		echo '<td>'.$row["current"].'</td><td>'.$row["N"]."</td>";
		echo '</tr>';
	  }
	  echo '</table>';
	} else {
	  echo "No results<br>";
	}
} catch(PDOException $e) {
	echo $sql . "<br>" . $e->getMessage();
}


echo '<p>All logging:<br>';
try {
	$stmt = $conn->prepare("select * from VisitLogs order by log_time desc");
	$stmt->execute();
	$result = $stmt->get_result();
	if ($result->num_rows > 0) {
	 echo '<table>';
	 while($row = $result->fetch_assoc()) {
		echo '<tr>';
		echo '<td>'.$row["visit_id"].'</td><td>'.$row["current"]."</td><td>".$row['target'].'</td><td>'.$row["log_time"].'</td>';
		echo '</tr>';
	  }
	  echo '</table>';
	} else {
	  echo "No results<br>";
	}
} catch(PDOException $e) {
	echo $sql . "<br>" . $e->getMessage();
}

$conn->close();

?> 
```
