# Homebrew analytics

These are some functions to log basic site-visitor behaviours: page counts and click counts. They both assume a database for the site has been set up; in my case, this was a MySQL database on Dreamhost.

## Page visit counter

This page counter keeps the saved information very basic - it doesn't save all the individual visits, just the tally per page and the date-time of the last visit.

First, to initialize the database table, or reset it to clean up, make and open a page with the below PHP code, with a chosen password as a GET variable:

```
<?php

$servername = "mysql.xxx.com";
$username = "xxx";
$password = "xxx";
$database = "xxx";
$page_password = 'xxx';

$reset_pw = $_GET["password"];
if ($reset_pw != $page_password) {
	return;
}

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully<br>";

try {	
	$sql = "use ".$database;
	$result = $conn->query($sql);
} catch(PDOException $e) {
	echo $sql . "<br>" . $e->getMessage();
}

try {
	$sql = "DROP TABLE IF EXISTS SiteMem";
	$result = $conn->query($sql);
} catch(PDOException $e) {
	echo $sql . "<br>" . $e->getMessage();
}

try {
	$sql = "CREATE TABLE IF NOT EXISTS SiteMem (
	id INT(6) AUTO_INCREMENT PRIMARY KEY,
	logtype VARCHAR(255),
	counter INT(6),
	URL VARCHAR(255),
	last_visit DATETIME
	)";
	$result = $conn->query($sql);
} catch(PDOException $e) {
	echo $sql . "<br>" . $e->getMessage();
}

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

To log how often a page is visited, just add the PHP code below into the headers of the pages to be logged.

```
<?php
try {
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

	try {	
		$sql = "use ".$db_name;
		$result = $conn->query($sql);
	} catch(PDOException $e) {
	}

	try {
		$sql = "CREATE TABLE IF NOT EXISTS SiteMem (
		id INT(6) AUTO_INCREMENT PRIMARY KEY,
		counter INT(6),
		URL VARCHAR(255),
		last_visit DATETIME
		)";
		$result = $conn->query($sql);
	} catch(PDOException $e) {
	}

	$this_URL = strtok($_SERVER["REQUEST_URI"], '?');
	$this_datetime = date('Y-m-d H:i:s');

	// Retrieve and increment counter

	$current_counter = 0;

	try {	
		$stmt = $conn->prepare("select counter from SiteMem where URL=?");
		$stmt->bind_param("s", $this_URL);
		$stmt->execute();
		$result = $stmt->get_result();
		if ($result->num_rows > 0) {
		  while($row = $result->fetch_assoc()) {
			$current_counter = $row["counter"];
		  }
		} else {
		  $current_counter = 0;
		  // Add row for this URL
		  $sql = "INSERT INTO SiteMem (counter, URL, last_visit) VALUES (0, ?, '".$this_datetime."')";
		  $stmt = $conn->prepare($sql);
		  $stmt->bind_param("s", $this_URL);
		  $stmt->execute();
		}
	} catch(PDOException $e) {
	}

	$current_counter++;

	// Write

	try {	
		$sql = "UPDATE SiteMem SET counter = ".$current_counter." WHERE URL=?";
		$stmt = $conn->prepare($sql);
		$stmt->bind_param("s", $this_URL);
		$stmt->execute();
	} catch(PDOException $e) {
	}

	$conn->close();

} catch (Exception $e) {

}

?> 
```

The following page lists out the visits in a table:

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

try {	
	$sql = "use ".$db_name;
	$result = $conn->query($sql);
} catch(PDOException $e) {
	echo $sql . "<br>" . $e->getMessage();
}

try {
	$stmt = $conn->prepare("select * from SiteMem order by counter desc");
	$stmt->execute();
	$result = $stmt->get_result();
	if ($result->num_rows > 0) {
	 echo '<table>';
	 while($row = $result->fetch_assoc()) {
		echo '<tr>';
		  //echo $row['URL']." --- ".$row["last_visit"]." --- ".$row["counter"]."<br>";
		echo '<td>'.$row["counter"]."</td><td>".$row['URL'].'</td><td>'.$row["last_visit"].'</td>';
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

## Click counter

These functions create a logging system for clicks on links; easy to adjust or generalize to other behaviours though.

First, as above, to initialize the database table, or reset it to clean up: Load a page with this code, and a chosen page password provided as a GET variable:

```
<?php
$servername = "mysql.xxx.com";
$username = "xxx";
$password = "xxx";
$database = "xxx";
$page_password = 'xxx';

$reset_pw = $_GET["password"];
if ($reset_pw != $page_password) {
	return;
}

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully<br>";

try {	
	$sql = "use ".$database;
	$result = $conn->query($sql);
} catch(PDOException $e) {
	echo $sql . "<br>" . $e->getMessage();
}

try {
	$sql = "DROP TABLE IF EXISTS ClicksMem";
	$result = $conn->query($sql);
} catch(PDOException $e) {
	echo $sql . "<br>" . $e->getMessage();
}

try {
	$sql = "CREATE TABLE IF NOT EXISTS ClicksMem (
	id INT AUTO_INCREMENT PRIMARY KEY,
	logtype VARCHAR(255),
	label VARCHAR(255),
	URL VARCHAR(255),
	clickdate DATETIME
	)";
	$result = $conn->query($sql);
} catch(PDOException $e) {
	echo $sql . "<br>" . $e->getMessage();
}

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

Then, on each page on which to register clicks on links, add this JavaScript script (note the highly annoying hack to avoid the request getting cached and only working once):

```

<script>
	document.addEventListener('mousedown', (event) => myFunction2(event))
	let myFunction2 = function(event) {
		let t = event.target;
		if (typeof t.href == "string") {
			const xhttp = new XMLHttpRequest();
			xhttp.open("GET", "/click_counter.php?val=" + t.href + "&anticache=" + Math.random(), true);
			xhttp.send();
		}
	}
</script>

```

This is the PHP file called in the JavaScript function, which adds an event to the database table. Note it's expected to sit in the website's root directory.

```
<?php
try {
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

	try {	
		$sql = "use ".$db_name;
		$result = $conn->query($sql);
	} catch(PDOException $e) {
	}

	// Write event
	$val = $_GET["val"];
	$this_datetime = date('Y-m-d H:i:s');

	try {	
		$sql = "INSERT INTO ClicksMem (URL, clickdate) VALUES (?, ?)";
		$stmt = $conn->prepare($sql);
		$stmt->bind_param("ss", $val, $this_datetime);
		$stmt->execute();
	} catch(PDOException $e) {
	}

	$conn->close();

} catch (Exception $e) {

}
?>
```

Finally, to list the clicked links:

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

try {	
	$sql = "use ".$db_name;
	$result = $conn->query($sql);
} catch(PDOException $e) {
	echo $sql . "<br>" . $e->getMessage();
}

try {
	$stmt = $conn->prepare("select * from ClicksMem");
	$stmt->execute();
	$result = $stmt->get_result();
	if ($result->num_rows > 0) {
	 echo '<table>';
	 while($row = $result->fetch_assoc()) {
		echo '<tr>';
		echo '<td>'.$row["URL"]."</td><td>".$row['clickdate'].'</td>';
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

