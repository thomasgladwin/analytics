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
	$stmt = $conn->prepare("select last_restart from LoggingInfo order by last_restart desc limit 1");
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

$page_to_test = "/papers.php";
echo '<p>Preceding Page Probability (P3); example for '.$page_to_test.':<br>';
try {
	$stmt = $conn->prepare("WITH CTE1 AS (SELECT DISTINCT visit_id, current, target from VisitLogs v1 where log_time < (SELECT MAX(log_time) from VisitLogs v2 WHERE v1.visit_id = v2.visit_id and current = '".$page_to_test."')), CTE2 AS (SELECT Count(*) as N, (Select Count(*) from CTE1) as Total, current FROM CTE1 Group By current Order By N desc) Select N/Total as Prob, current From CTE2");
	$stmt->execute();
	$result = $stmt->get_result();
	if ($result->num_rows > 0) {
	 echo '<table>';
	 while($row = $result->fetch_assoc()) {
		echo '<tr>';
		echo '<td>'.$row["Prob"].'</td><td>'.$row["current"].'</td>';
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
