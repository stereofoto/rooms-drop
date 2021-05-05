<?php

clean_uploads();
auth_requiered();

$d = $_GET['d'] ?? false;
if (!check_drop_name($d)) {
  header("Location: ./");
  exit();
}
create_drop($d);

?><!DOCTYPE html>
<html>
  <head>
    <title>ROOMS drop</title>
    <link rel="icon" href="data:,">
    <link rel="stylesheet" href="style.css">
    <script>
const photos_list = [];
let current_xhr = null;
let drop_name = "<?php echo $d; ?>";
function upload() {
  if (current_xhr) return;
  let i;
  for (i = 0; i < photos_list.length; i++)
    if (!photos_list[i].uploaded)
      break;
  if (i < photos_list.length) {
    const formData = new FormData()
    formData.append('file', photos_list[i].file);
    formData.append('drop', drop_name);
    formData.append('action', 'upload');

    photos_list[i].uploading = true;
    current_xhr = new XMLHttpRequest();
    current_xhr.open('POST', './', true);
    current_xhr.upload.addEventListener("progress", e => {
      photos_list[i].bar.style.width = ((e.loaded * 100.0 / e.total) || 100) + '%';
    });
    current_xhr.addEventListener('readystatechange', e => {
      if (current_xhr.readyState == 4 && current_xhr.status == 200) {
        photos_list[i].uploaded = true;
        photos_list[i].uploading = false;
        current_xhr = null;
        upload();
      }
      else if (current_xhr.readyState == 4 && current_xhr.status != 200) {
        current_xhr = null;
        photos_list[i].uploading = false;
	photos_list[i].bar.style.width = "0%";
      }
    });
    current_xhr.send(formData);
  } else {
    const json = {
      "media": [],
      "meta": { "url_base": (new URL("./", document.location)).href }
    };
    for (let i = 0; i < photos_list.length; i++) {
      const p = { "url": "?d="+drop_name+"&f="+encodeURIComponent(photos_list[i].filename) };
      if (photos_list[i].url) p.url = photos_list[i].url;
      if (photos_list[i].swapped) p.swapped = true;
      if (photos_list[i].phantogram) p.phantogram = true;
      json.media.push(p);
    }
    current_xhr = new XMLHttpRequest();
    current_xhr.open('POST', './', true);
    current_xhr.addEventListener('readystatechange', e => {
      if (current_xhr.readyState == 4 && current_xhr.status == 200) {
        current_xhr = null;
      }
      else if (current_xhr.readyState == 4 && current_xhr.status != 200) {
        current_xhr = null;
      }
    });
    const formData = new FormData()
    formData.append('json', JSON.stringify(json, null, '\t'));
    formData.append('drop', drop_name);
    formData.append('action', 'upload');
    current_xhr.send(formData);
  }
}
function photoline(i, f, p, swapped, phantogram) {
  const div = document.createElement("div");
  div.classList.add("photos_line");
  const spanbarout = document.createElement("span");
  spanbarout.classList.add("gauge");
  const spanbarin = document.createElement("span");
  spanbarin.style.width = p+"%";
  spanbarout.appendChild(spanbarin);
  const img = document.createElement("img");
  img.width = "300";
  div.appendChild(spanbarout);
  div.appendChild(img);
  if (f instanceof File) {
    const reader = new FileReader();
    reader.readAsDataURL(f);
    reader.onloadend = function() {
      img.src = reader.result;
    }
  } else {
    img.src = f;
  }
  photos.appendChild(div);
  const label_swapped = document.createElement("label");
  const chk_swapped = document.createElement("input");
  chk_swapped.type = "checkbox";
  if (swapped) chk_swapped.checked = true;
  chk_swapped.setAttribute("data-i", i);
  chk_swapped.onchange = e => {
    photos_list[chk_swapped.getAttribute("data-i")].swapped = chk_swapped.checked;
  };
  label_swapped.appendChild(chk_swapped);
  label_swapped.appendChild(document.createTextNode("cross"));
  div.appendChild(label_swapped);
  const label_phantogram = document.createElement("label");
  const chk_phantogram = document.createElement("input");
  chk_phantogram.type = "checkbox";
  if (phantogram) chk_phantogram.checked = true;
  chk_phantogram.setAttribute("data-i", i);
  chk_phantogram.onchange = e => {
    photos_list[chk_phantogram.getAttribute("data-i")].phantogram = chk_phantogram.checked;
  };
  label_phantogram.appendChild(chk_phantogram);
  label_phantogram.appendChild(document.createTextNode("phantogram"));
  div.appendChild(label_phantogram);
  const btn = document.createElement("button");
  btn.value = "Delete";
  btn.onclick = upload;

  return spanbarin;
}
window.addEventListener("DOMContentLoaded", (event) => {
  const url_info = document.getElementById("url_info");
  const photos = document.getElementById("photos");
  const drop = document.getElementById("drop");

  {
    const url = new URL("./?d="+drop_name+"&f=list.json", document.location);
    const input = document.createElement("input");
    input.id = "json_url";
    input.type = "text"
    input.readOnly = true;
    input.value = url;
    url_info.appendChild(input);
    
    current_xhr = new XMLHttpRequest();
    current_xhr.open('GET', url, true);
    current_xhr.addEventListener('readystatechange', e => {
      if (current_xhr.readyState == 4 && current_xhr.status == 200) {
        json = JSON.parse(current_xhr.responseText);
        current_xhr = null;
        let i = 0;
        json.media.forEach(p => {
          const spanbarin = photoline(i, p.url, 100, p.swapped, p.phantogram);
          i += 1;
          photos_list.push({
            "file": null,
            "filename": p.url,
            "url": p.url,
            "bar": spanbarin,
            "swapped": p.swapped,
            "phantogram": p.phantogram,
            "uploading": false,
            "uploaded": true,
          });
        });
        document.getElementById("btn_update").classList.add("show");
        document.getElementById("btn_update").onclick = upload;
      }
      else if (current_xhr.readyState == 4 && current_xhr.status != 200) {
        current_xhr = null;
      }
    });
    current_xhr.send('');
  }

  document.getElementById("btn_delete").onclick = () => {
    let list;
    try {
      list = JSON.parse(localStorage.getItem('drops_list'));
      if (!list) list = [];
    } catch (JSONError) {
      list = []
    }
    list = list.filter(item => item !== drop_name);
    localStorage.setItem('drops_list', JSON.stringify(list));
    document.location = "./?action=delete&d="+drop_name;
  }

  const hl = e => {
    e.preventDefault();
    e.stopPropagation();
    drop.classList.add("highlight");
  };
  const uhl = e => {
    e.preventDefault();
    e.stopPropagation();
    drop.classList.remove("highlight");
  };
  drop.addEventListener("dragenter", hl, false);
  drop.addEventListener("dragover", hl, false);
  drop.addEventListener("dragleave", uhl, false);
  drop.addEventListener("drop", e => {
    uhl(e);
    const files = e.dataTransfer.files;
    for (let i = 0; i < files.length; i++) {
      const f = files[i];
      const spanbarin = photoline(i, f, 0, false, false);
      photos_list.push({
        "file": f,
        "filename": f.name,
        "bar": spanbarin,
        "swapped": false,
        "phantogram": false,
        "uploading": false,
        "uploaded": false,
      });
    }
    document.getElementById("btn_update").classList.add("show");
    document.getElementById("btn_update").onclick = upload;
    upload();
  }, false);
});
    </script>
  </head>
  <body>
    <h1><a href="./">«</a> ROOMS drop</h1>
    <p id="url_info"></p>
    <div id="drop">DROP PHOTOS HERE</div>
    <div id="photos"></div>
    <p><input id="btn_update" type="button" value="Update" /></p>
    <p><input id="btn_delete" type="button" value="Delete this drop" /></p>
  </body>
</html>