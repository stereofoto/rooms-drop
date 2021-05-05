<?php

auth_requiered();

$d = $_POST['drop'] ?? false;
if (!check_drop_name($d)) {
  header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
  exit();
}

$target_dir = "#uploads/".$d."/";
if ($_FILES["file"] ?? false && $_FILES["file"]["error"] == 0) {
  $target_file = $target_dir . basename($_FILES["file"]["name"]);
  $check = getimagesize($_FILES["file"]["tmp_name"]);
  if($check !== false) {
    if (!is_dir($target_dir)) {
      mkdir($target_dir);
      file_put_contents($target_dir.'/date.txt', time());
    }
    move_uploaded_file( $_FILES['file']['tmp_name'], $target_file);
  } else {
    header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
    exit();
  }
} else if ($_POST["json"]) {
  file_put_contents($target_dir.'/list.json', $_POST["json"]);
}

?>