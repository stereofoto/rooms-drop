<?php

auth_requiered();

$d = $_GET['d'] ?? false;
if (!check_drop_name($d)) {
  header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
  exit();
}

$f = $_GET['f'] ?? false;
if ($f) {
  $file = "#uploads/".$d."/".basename($f);
  unlink($file);
} else {
  remove_drop($d);
  header("Location: ./");
}

?>
