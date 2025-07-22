<html>
<head><title>Visit info</title></head>
<body style="margin-left:5%;">
<h1>Visit logging info</h1>
<?php
require("open_conn.php");

$reset_pw = $_GET["password"];
if ($reset_pw != $show_info_password) {
	return;
}
try {	
	$sql = "use ".$db_name;
	$result = $conn->query($sql);
} catch(PDOException $e) {
	echo $sql . "<br>" . $e->getMessage();
}

echo '<p>Database last reset at: ';

try {
	$stmt = $conn->prepare("select last_restart from LoggingInfo order by last_restart desc limit 1");
	$stmt->execute();
	$result = $stmt->get_result();
	if ($result->num_rows > 0) {
	 while($row = $result->fetch_assoc()) {
		echo $row["last_restart"];
		$last_restart_str = $row["last_restart"];
		#$now = time();
		#$last_restart = strtotime($last_restart_str);
		#$datediff = $now - $last_restart;
		#$datediff_days = round($datediff / (60 * 60 * 24));
		$last_restart = new DateTime($last_restart_str);
		$now = new DateTime();
		$datediff_days = (int)($last_restart->diff($now)->format("%a"));
		echo ". Days ago: ".$datediff_days;
	  }
	} else {
	  echo "No results<br>";
	  $datediff = 0;
	}
} catch(PDOException $e) {
	echo $sql . "<br>" . $e->getMessage();
}

echo '<h2>Number of visits</h2>';
try {
	$stmt = $conn->prepare('SELECT COUNT(*) as N FROM (SELECT DISTINCT visit_id from VisitLogs) SQ');
	$stmt->execute();
	$result = $stmt->get_result();
	while($row = $result->fetch_assoc()) {
		if ($result->num_rows > 0) {
			echo "Number of visits: ".$row["N"].". ";
			if ($datediff_days > 0) {
				$vpd = round(((float)$row["N"]) / $datediff_days, 2);
				echo "Visits per day: ".$vpd.".<br>";
			}
		} else {
			echo "No results<br>";
		}
	}
} catch(PDOException $e) {
	echo $sql . "<br>" . $e->getMessage();
}
try {
	$stmt = $conn->prepare('SELECT count(*) as N FROM (SELECT DISTINCT visit_id from VisitLogs where target != "Open" and target != "Close") SQ');
	$stmt->execute();
	$result = $stmt->get_result();
	while($row = $result->fetch_assoc()) {
		if ($result->num_rows > 0) {
			echo "Number of visits with any clicks: ".$row["N"].". ";
			if ($datediff_days > 0) {
				$vpd = round(((float)$row["N"]) / $datediff_days, 2);
				echo "Visits per day: ".$vpd.".<br>";
			}
		} else {
			echo "No results<br>";
		}
	}
} catch(PDOException $e) {
	echo $sql . "<br>" . $e->getMessage();
}

echo '<h2>Page visit counter (one count per visit)</h2>';
try {
	$stmt = $conn->prepare('WITH CTE1 AS (select distinct visit_id, current from VisitLogs where target = "Open" order by log_time desc) Select current, COUNT(*) as N FROM CTE1 GROUP BY CURRENT order by N desc;');
	$stmt->execute();
	$result = $stmt->get_result();
	if ($result->num_rows > 0) {
	 echo '<table>';
	 while($row = $result->fetch_assoc()) {
		echo '<tr>';
		echo '<td>'.$row["N"].'</td><td>'.$row["current"]."</td>";
		echo '</tr>';
	  }
	  echo '</table>';
	} else {
	  echo "No results<br>";
	}
} catch(PDOException $e) {
	echo $sql . "<br>" . $e->getMessage();
}

echo '<h2>Page visit duration [s]</h2>';
try {
	$stmt = $conn->prepare('with CTE1 as (
    select current, timeOnPage, row_number() over (PARTITION BY Current ORDER by timeOnPage) as row_id, (select count(1) from VisitLogs v2 where v1.current = v2.current and target = "Close") as ct from VisitLogs v1 where target = "Close"
)
select current, round(avg(timeOnPage)/1000, 1) as duration from CTE1 where row_id between ct/2.0 and ct/2.0 + 1 group by current;
');
	$stmt->execute();
	$result = $stmt->get_result();
	if ($result->num_rows > 0) {
	 echo '<table>';
	 while($row = $result->fetch_assoc()) {
		echo '<tr>';
		echo '<td>'.$row["duration"].'</td><td>'.$row["current"]."</td>";
		echo '</tr>';
	  }
	  echo '</table>';
	} else {
	  echo "No results<br>";
	}
} catch(PDOException $e) {
	echo $sql . "<br>" . $e->getMessage();
}

echo '<h2>Clicks</h2>';
try {
	$stmt = $conn->prepare('Select * from VisitLogs where target <> "Open" and target <> "Close" order by log_time desc');
	$stmt->execute();
	$result = $stmt->get_result();
	if ($result->num_rows > 0) {
	 echo '<table>';
	 while($row = $result->fetch_assoc()) {
		echo '<tr>';
		echo '<td>'.$row["ip"].'</td><td>'.$row["visit_id"].'</td><td>'.$row["current"]."</td><td>".$row['target'].'</td><td>'.$row["log_time"].'</td>';
		echo '</tr>';
	  }
	  echo '</table>';
	} else {
	  echo "No results<br>";
	}
} catch(PDOException $e) {
	echo $sql . "<br>" . $e->getMessage();
}

echo '<h2>Preceding Page Probability (P3) - likelihood a page was opened during a visit before a given target page</h2>';
try {
	$stmt = $conn->prepare("WITH CTE_Final AS (
WITH
	CTE_Outer AS (
        Select distinct v1.current as Page, v2.current as Prepage from VisitLogs v1
		left join VisitLogs v2 on v2.current <> v1.current
	)
	SELECT *, (
	WITH
    	CTE1 AS (
            SELECT DISTINCT visit_id, current, target from VisitLogs v1 
            where log_time < (SELECT MAX(log_time) from VisitLogs v2 WHERE v1.visit_id = v2.visit_id and current = CTE_Outer.Page)
        ),
    	CTE2 AS (
            SELECT Count(*) as N, (Select Count(*) from CTE1) as Total, current FROM CTE1 
            Group By current Order By N desc
        )
    Select N/Total as Prob From CTE2 where CTE2.current = CTE_Outer.Prepage
	) as Prob FROM CTE_Outer ORDER BY Page, Prob desc
)
SELECT * From CTE_Final WHERE Prob is not null ORDER BY Page, Prob desc");
	$stmt->execute();
	$result = $stmt->get_result();
	if ($result->num_rows > 0) {
	 echo '<table>';
		echo '<tr>';
		echo '<td>'."Target page".'</td><td>'."Preceding page".'</td><td>'."Probability".'</td>';
		echo '</tr>';
	 while($row = $result->fetch_assoc()) {
		echo '<tr>';
		echo '<td>'.$row["Page"].'</td><td>'.$row["Prepage"].'</td><td>'.$row["Prob"].'</td>';
		echo '</tr>';
	  }
	  echo '</table>';
	} else {
	  echo "No results<br>";
	}
} catch(PDOException $e) {
	echo $sql . "<br>" . $e->getMessage();
}

echo '<h2>All logging</h2>';
try {
	$stmt = $conn->prepare("select * from VisitLogs order by log_time desc");
	$stmt->execute();
	$result = $stmt->get_result();
	if ($result->num_rows > 0) {
	 echo '<table>';
	 while($row = $result->fetch_assoc()) {
		echo '<tr>';
		echo '<td>'.$row["ip"].'</td><td>'.$row["visit_id"].'</td><td>'.$row["current"]."</td><td>".$row['target'].'</td><td>'.$row["log_time"].'</td>';
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
</body>
</html>