	<script>
		let timeStart = Date.now();
		<?php
			session_start();
			if (!isset($_SESSION['VisitID'])) {
				$_SESSION['VisitID'] = 'VID'.strval(rand());
			}
			$vid = $_SESSION['VisitID'];
			echo "let vid = '$vid';";
			if (!isset($_SESSION['php_access_codes'])) {
				$_SESSION['php_access_codes'] = 0;
			}
			$_SESSION['SERVER_SERVER_ADDR'] = $_SERVER['SERVER_ADDR'];
			//echo 'console.log("H Session_id: '.session_id().'");';
			//echo 'console.log("H Session php_access_codes: '.$_SESSION['php_access_codes'].'");';
		?>
		
		document.addEventListener('mousedown', (event) => myFunctionMouseDown(event));
		document.addEventListener('keydown', (event) => myFunctionMouseDown(event));
		window.addEventListener('beforeunload', (event) => myFunctionUnload(event));
		
		document.addEventListener('DOMContentLoaded', function() {
			//window.alert("Testing 2 ");
			var xhttpOnload = new XMLHttpRequest();
			xhttpOnload.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200) {
					//document.getElementById("demo").innerHTML = xhttpOnload.responseText;
					//window.alert("Testing " + xhttpOnload.responseText);
				}
			}
			var home_url = window.location.protocol + "//" + window.location.hostname;
			//console.log("Testing 7: " + window.location.protocol + "//" + window.location.hostname);
			arg0 = home_url + "/analyticsphp/VisitLogs.php?vid=" + vid + "&current=" + location.pathname + "&target=Open&anticache=" + Math.random();
			xhttpOnload.open("GET", arg0, true);
			xhttpOnload.send();
		}, false);

		let myFunctionMouseDown = function(event) {
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
				let timeOnClose = Date.now();
				let timeOnPage = timeOnClose - timeStart;
				var home_url = window.location.protocol + "//" + window.location.hostname;
				arg0 = home_url + "/analyticsphp/VisitLogs.php?vid=" + vid + "&current=" + location.pathname + "&target=" + t.href + "&anticache=" + Math.random() + "&timeOnPage=" + timeOnPage;
				xhttp.open("GET", arg0, true);
				xhttp.send();
				//window.alert("Testing 2 ");
				let time0 = Date.now();
				let time1 = Date.now();
				while ((time1 - time0) < 20) {
					//console.log(time1 - time0);
					time1 = Date.now();
				}					
			}
		}

		let myFunctionUnload = function(event) {
			var xhttp = new XMLHttpRequest();
			var xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200) {
					//document.getElementById("demo").innerHTML = xhttp.responseText;
					//window.alert("Testing " + xhttp.responseText);
				}
			}
			let timeOnClose = Date.now();
			let timeOnPage = timeOnClose - timeStart;
			var home_url = window.location.protocol + "//" + window.location.hostname;
			arg0 = home_url + "/analyticsphp/VisitLogs.php?vid=" + vid + "&current=" + location.pathname + "&target=Close&anticache=" + Math.random() + "&timeOnPage=" + timeOnPage;
			xhttp.open("GET", arg0, true);
			xhttp.send();
			//window.alert("Testing 2 ");
			let time0 = Date.now();
			let time1 = Date.now();
			while ((time1 - time0) < 20) {
				//console.log(time1 - time0);
				time1 = Date.now();
			}			
		}

		//window.alert("Testing " + vid);
		
	</script>