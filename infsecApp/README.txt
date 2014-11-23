Starting point: 
start.php - if you have already registered a username to your IMEI
OR
register.php - if you want to register a username to your IMEI

======================

store.php can be called from the android phone to store the coordinates

======================

map.php is called either from start.php or register.php and should not be called on its own. It contains the main point, the Google Map.

phpsql.php is used by map.php to query the location data of a given IMEI, which is then used to create the markers.
