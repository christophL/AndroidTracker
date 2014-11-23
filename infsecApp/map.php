<!DOCTYPE html>
<html>
  <head>
    <style>
      #map_canvas {
        width: 1000px;
        height: 600px;
      }
    </style>
    <script src="https://maps.googleapis.com/maps/api/js"></script>
    <script>
	//This function is used to parse the http GET value given to this site
	function getQueryVariable(variable)
	{
		   var query = window.location.search.substring(1);
		   var vars = query.split("&");
		   for (var i=0;i<vars.length;i++) {
		           var pair = vars[i].split("=");
		           if(pair[0] == variable){return pair[1];}
		   }
		   return(false);
	}

	function load() {
    	var map = new google.maps.Map(document.getElementById("map_canvas"), {
        	center: new google.maps.LatLng(47.2649028, 11.3963183),
        	zoom: 14,
        	mapTypeId: 'roadmap'
    	});
   		var infoWindow = new google.maps.InfoWindow;

      	// 
		var imei = getQueryVariable("IMEI");
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
		//now print the sorted list using the iteration variable as icon for the markers
        for (var i = 0; i < markersArray.length; i++) {
          var point = new google.maps.LatLng(
              parseFloat(markersArray[i].getAttribute("lat")),
              parseFloat(markersArray[i].getAttribute("long")));
          var marker = new google.maps.Marker({
            map: map,
            position: point,
			icon: "http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld="+i+"|AEEEEE|000000"
          });
	//to add information after clicking on marker
          //bindInfoWindow(marker, map, infoWindow, html);
        }
      });
    }

    function downloadUrl(url, callback) {
      var request = window.ActiveXObject ?
          new ActiveXObject('Microsoft.XMLHTTP') :
          new XMLHttpRequest;

      request.onreadystatechange = function() {
        if (request.readyState == 4) {
          request.onreadystatechange = doNothing;
          callback(request, request.status);
        }
      };

      request.open('GET', url, true);
      request.send(null);
    }

	//TODO: why do we need this function?
    function doNothing() {}

    </script>
  </head>
  <body onload="load()">
    <div id="map_canvas"></div>
  </body>
</html>


