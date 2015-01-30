Starting point: 
index.php

======================

store.php can be called from the android phone to store the coordinates

======================

map.php is called from index.php and should not be called on its own. It contains the main point, the Google Map.

phpsql.php is used by map.php to query the location data of a given IMEI, which is then used to create the markers.

======================
Command used to create the used certificate:

openssl req -new -x509 -days 365 -sha1 -newkey rsa:2048 -nodes -keyout server.key -out server.crt
