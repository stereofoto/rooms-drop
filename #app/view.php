<?php

clean_uploads();

$d = $_GET['d'] ?? false;
if (!check_drop_name($d)) {
  header("Location: ./");
  exit();
}

?><!DOCTYPE html>
<html>
  <head>
    <title>ROOMS drop</title>
    <link rel="icon" href="data:,">
    <link rel="stylesheet" href="style.css">
  </head>
  <body>
    <h1><a href="./">«</a> ROOMS drop</h1>

    <script type="text/javascript">
      var json_url = './?d=<?php echo $d; ?>&f=list.json';
      window.addEventListener('message', function(e) {
        if (e.origin == 'https://stereopix.net') {
          if (e.data.type == 'viewerReady') {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', json_url);
            xhr.onload = function () {
              if (xhr.readyState == 4 && xhr.status == 200) {
                var json = JSON.parse(xhr.responseText);
                e.source.postMessage({'stereopix_action': 'list_add_json', 'media': json}, 'https://stereopix.net');
                e.source.focus();
              }
            };
            xhr.send(null);
          }
        }
      });
    </script>
    <iframe title="Stereoscopic (3D) photo viewer" 
      style="width: 100%; height: 960px; max-height: 100vh; max-width: 100vw; border: 2px solid black; margin: 8px 0;" 
      allowfullscreen="yes" allowvr="yes" allow="fullscreen;xr-spatial-tracking;accelerometer;gyroscope" 
      src="https://stereopix.net/viewer:embed/"></iframe>
  </body>
</html>
