<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">

<html>

<head>

	<script>
		<?php
			try {
				session_start();
			} catch(Exception $e) {
			}
		?>
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
			<?php
				require("logging_passwords.php");
				echo "let get_pw = '$password_get';";
			?>
			arg0 = "/VisitLogs.php?vid=" + vid + "&current=" + location.pathname + "&target=0&anticache=" + Math.random() + "&password=" + get_pw;
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
				arg0 = "/VisitLogs.php?vid=" + vid + "&current=" + location.pathname + "&target=" + t.href + "&anticache=" + Math.random() + "&password=ghjkhgj45634fdg";
				xhttp.open("GET", arg0, true);
				xhttp.send();
				//window.alert("Testing 2 ");
			}
		}

		//window.alert("Testing " + vid);
		
	</script>

</head>
<body>

