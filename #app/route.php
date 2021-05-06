<?php

include("fcts.php");
$action = $_POST['action'] ?? $_GET['action'] ?? 'none';

if ($action == 'none') {
  if ($_GET['d'] ?? false) {
    if ($_GET['f'] ?? false) {
      include('file.php');
    } else if ($_GET['k'] ?? false) {
      include('drop.php');
    } else {
      include('view.php');
    }
  } else {
    include('frontpage.php');
  }
} else if ($action == 'upload') {
  include('upload.php');
} else if ($action == 'delete') {
  include('delete.php');
} else if ($action == 'logout') {
  auth_requiered(true);
} else {
  header('Location: ./');
}

?>
