<?php

$defaultfile = 'list.json';
$accepted_extensions = array('webp', 'jpg', 'jpeg', 'png', 'json');
$accepted_origins = '*'; // or array('https://stereopix.net');

$d = $_GET['d'] ?? false;
if (!check_drop_name($d)) $d = '___';
$file = '#uploads/'.$d.'/'.pathinfo($_GET['f'] ?? $defaultfile, PATHINFO_BASENAME); // Allow only files from this directory /!\
if (in_array(pathinfo($file, PATHINFO_EXTENSION), $accepted_extensions) && file_exists($file)) {
        $lastModified = filemtime($file);
        $etag = md5($lastModified);
        $ifModifiedSince = strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE'] ?? false);
        $etagHeader = trim($_SERVER['HTTP_IF_NONE_MATCH'] ?? '');
        $origin = $_SERVER['HTTP_ORIGIN'] ?? false;
        if ($origin) {
                header('Vary: Origin');
                if ($accepted_origins == '*')
                        header('Access-Control-Allow-Origin: *');
                else if (in_array($origin, $accepted_origins))
                        header('Access-Control-Allow-Origin: '.$origin);
                else
                        header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden') && exit;
        }
        header('Last-Modified: '.gmdate('D, d M Y H:i:s', $lastModified).' GMT');
        header('Etag: '.$etag);
        header('Cache-Control: public');
        if ($etagHeader == $etag || $ifModifiedSince == $lastModified) {
                header($_SERVER['SERVER_PROTOCOL'].' 304 Not Modified');
        } else {
                header('Content-Type: '.mime_content_type($file));
                header('Content-Length: '.filesize($file));
                readfile($file);
        }
} else {
        header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
        if (substr($file, -9) == 'list.json') echo '{}';
}
?>