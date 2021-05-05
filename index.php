<?php

///////////////////
// Configuration //
///////////////////

/*
Storage limit in seconds
0 means forever

The deletion is actually performed when someone visits the page after this delay.
*/
$CONFIG_STORAGE_TIME = 3 * 24 * 60 * 60;

/*
Should the app show the storage bar
Boolean
*/
$CONFIG_SHOW_STORAGE = true;

/*
Configuration to limit who can upload files
Boolean false or associative array

To enable authentification, the value should be an array whose keys are the usernames allowed to connect and associated values are the MD5 hashes of the string "<username>:ROOMS-drop:<password>" computed for each user. It is better to compute it offline so that the actual password is not stored on the server [it will not be transmitted neither].
The associated value can be computed with the following unix command: echo -n "<username>:ROOMS-drop:<password>" | md5sum

Example, with username "user" and password "pass", you can set:
$CONFIG_AUTH = array(
  'user' => '9e3acb584e593b824507346b99dc0fad',
);
*/
$CONFIG_AUTH = false;

/////////
// App //
/////////

include("#app/route.php");

?>
