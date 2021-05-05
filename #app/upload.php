<?php

auth_requiered();
function not_uploaded() {
  header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden');
  exit();
}

$d = $_POST['drop'] ?? false;
if (!check_drop_name($d)) not_uploaded();

$target_dir = "#uploads/".$d."/";
if (($_FILES["file"] ?? false) && $_FILES["file"]["error"] == 0) {
  $target_file = $target_dir . basename($_FILES["file"]["name"]);
  $check = getimagesize($_FILES["file"]["tmp_name"]);
  if($check !== false) {
    if (!is_dir($target_dir)) create_drop($d);
    move_uploaded_file( $_FILES['file']['tmp_name'], $target_file);
  } else {
    not_uploaded();
  }
} else if ($_POST["json"]) {
  file_put_contents($target_dir.'/list.json', $_POST["json"]);
} else {
  not_uploaded();
}

?>