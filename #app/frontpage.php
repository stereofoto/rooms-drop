<?php

clean_uploads();
auth_requiered();

?><!DOCTYPE html>
<html>
  <head>
    <title>ROOMS drop</title>
    <link rel="icon" href="data:,">
    <link rel="stylesheet" href="style.css" />
    <script>
window.addEventListener("DOMContentLoaded", (event) => {
  const yourdrops = document.getElementById("yourdrops");
  let list;
  try {
    list = JSON.parse(localStorage.getItem('drops_list'));
    if (!list) list = [];
  } catch (JSONError) {
    list = []
  }
  list.forEach(d => {
    const p = document.createElement("p");
    const a = document.createElement("a");
    a.href = "?d=" + d;
    a.appendChild(document.createTextNode(d));
    p.appendChild(a);
    yourdrops.appendChild(p);
  });
  document.getElementById("adddrop").addEventListener('click', e => {
    e.preventDefault();
    const d = Math.random().toString(36).slice(-7);
    list.push(d);
    localStorage.setItem('drops_list', JSON.stringify(list));
    document.location = "?d=" + d;
  });
});
    </script>
  </head>
  <body>
    <h1>ROOMS drop</h1>
    <p>This little webapp is a companion for <a href="https://rooms.stereopix.net/">https://rooms.stereopix.net/</a> to help people to host their images<?php if ($CONFIG_STORAGE_TIME) echo ' temporary'; ?>.</p>
    <?php if ($CONFIG_SHOW_STORAGE) echo '<p>Storage: <span class="gauge"><span style="width: '.(100*disk_free_space(".")/ disk_total_space(".")).'%"></span></span></p>'; ?>
    <?php if ($CONFIG_STORAGE_TIME) echo '<p>Maximum storage time: '.time2str($CONFIG_STORAGE_TIME).'</p>'; ?>
    <?php if ($CONFIG_AUTH) echo '<p><a href="?action=logout">Logout</a></p>'; ?>
    <h2>Your drops</h2>
    <div id="yourdrops"></div>
    <p><a id="adddrop" href="#">Add a drop</a></p>
  </body>
</html>