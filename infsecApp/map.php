<!DOCTYPE html>
<?php
	//check whether user is logged in
	session_start();
	if($_SESSION['authenticated'] != 'yes') {
		exit("You are not authorised to access this page!");
	}

	$imei = $_GET['IMEI'];
?>
<html>
  <head>

	<link rel="stylesheet" type="text/css" href="css/style.css">
   	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js"></script>
    <script>
	
	//This function is used to parse the http GET value given to this site
	function getQueryVariable(variable)
	{
		   var query = window.location.search.substring(1);
		   var vars = query.split("&");
		   for (var i=0;i<vars.length;i++) {
		           var pair = vars[i].split("=");
		           if(pair[0] == variable){
						return pair[1];
					}
		   }
		   return(false);
	}

	function load() {
		//create the map
    	var map = new google.maps.Map(document.getElementById("map_canvas"), {
        	center: new google.maps.LatLng(47.2649028, 11.3963183),
        	zoom: 14,
        	mapTypeId: 'roadmap'
    	});
   		var infoWindow = new google.maps.InfoWindow;

		var imei = getQueryVariable("IMEI");

		//phpsql queries the database for all coordinates associated with the given IMEI
		//and returns it as a xml-file
     	downloadUrl("phpsql.php?IMEI="+imei, function(data) {
        var xml = data.responseXML;
        var markers = xml.documentElement.getElementsByTagName("marker");
		//sort the node list contained in markers according to their timestamp
		var markersArray = Array.prototype.slice.call(markers,0);
		markersArray.sort(function(a,b) {
			var timeA = a.getAttribute("time");
			var timeB = b.getAttribute("time");
			if(timeA > timeB) return 1;
			if(timeA < timeB) return -1;
			return 0;
		});

		//save all created points
		var coordinates = [];
		var point;
		//now print the sorted list using the iteration variable as icon for the markers
        for (var i = 0; i < markersArray.length; i++) {
          point = new google.maps.LatLng(
              parseFloat(markersArray[i].getAttribute("lat")),
              parseFloat(markersArray[i].getAttribute("long")));
		  coordinates[i] = point;
		  var text = markersArray[i].getAttribute("time").concat("\nAccuracy: +-").concat(markersArray[i].getAttribute("accuracy")).concat(" meters");
          var marker = new google.maps.Marker({
            map: map,
            position: point,
			icon: "http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld="+i+"|AEEEEE|000000",
			title: text
          });
		  //add circle showing the accuracy
		  var accuracy = markersArray[i].getAttribute("accuracy");
		  if(accuracy > 0) {
			  var options = {
				center: point,
				map: map,
				strokeColor: '#000000',
				radius:  parseInt(accuracy)
			  }
			  var circle = new google.maps.Circle(options);
		  }
		  //set center of google map to last point
		  map.setCenter(point);
        }

	  //add line to connect markers to form path
	  var path = new google.maps.Polyline({
		path: coordinates,
		geodesic: true,
		strokeColor: '#000000',
		strokeOpacity: 0.5,
		strokeWeight: 2
	  });

	  path.setMap(map);

      });
    }

    function downloadUrl(url, callback) {
      var request = window.ActiveXObject ?
          new ActiveXObject('Microsoft.XMLHTTP') :
          new XMLHttpRequest;

	  //add listener to the status of the XMLHttpRequest
      request.onreadystatechange = function() {
		//readyState == 4 => request finished and response is ready
        if (request.readyState == 4) {
		  //if request state changes again, do nothing
          request.onreadystatechange = doNothing;
		  //forward the result of the request (xml) to the callback function
          callback(request, request.status);
        }
      };

	  //send request to server
      request.open('GET', url, true);
      request.send(null);
    }

    function doNothing() {}

    </script>
	<script>
		$(document).ready(function(){
		  $("#lockButton").click(function(){
			$("#lockDiv").slideToggle();
		  });
		});
	</script>

	<script>
		$(document).ready(function(){
		  $("#wipeButton").click(function(){
			var wipe = confirm("Do you really want to wipe your whole device?");
			if(wipe == true) {
				document.forms["wipeForm"].submit();
			}
		  });
		});
	</script>

  </head>
  <body onload="load()">
	<div class="appName">
	AndroidTracker
	</div>
	<div id="contentDiv">
	<div id="menu">
		<button id="lockButton">Lock your device</button>
		<button id="wipeButton">Wipe your device</button>
		<FORM NAME ="logout" METHOD ="POST" ACTION ="logout.php">
		<INPUT class="menuButton" TYPE = "Submit" Name = "Submit1"  VALUE = "Logout">
		</FORM>
		<div id="lockDiv">
					<FORM NAME ="form1" METHOD ="POST" ACTION ="lock.php">
						<table>
							<tr>
								<td>New password:</td> <td><INPUT TYPE = 'PASSWORD' Name ='password'  value="" maxlength="100"></td>
							</tr>
							<tr>
								<td>Repeat password:</td> <td> <INPUT TYPE = 'PASSWORD' Name ='password_repeat'  value="" maxlength="100"> </td>
							</tr>
							<tr style="display:none">
								<td></td> <td> <INPUT TYPE = 'TEXT' Name ='imei'  value="<?PHP print $imei;?>" maxlength="15"> </td>
							</tr>
				
							<tr>
								<td colspan="2"><INPUT class="centerButton" TYPE = "Submit" Name = "Submit1"  VALUE = "Note lockdown request!"></td>
							</tr>
						</table>
					</FORM>
		</div>
		<div id="wipeDiv">
					<FORM NAME ="wipeForm" METHOD ="POST" ACTION ="wipe.php">
						<table>
							<tr style="display:none">
								<td></td> <td> <INPUT TYPE = 'TEXT' Name ='imei'  value="<?PHP print $imei;?>" maxlength="15"> </td>
							</tr>
				
							<tr>
								<td colspan="2"><INPUT class="centerButton" TYPE = "Submit" Name = "Submit1"  VALUE = "Note lockdown request!"></td>
							</tr>
						</table>
					</FORM>
		</div>
	</div>
	<div class="placeholderDiv">

	</div>

    <div id="map_canvas">

	</div>

	</div>
  </body>
</html>


