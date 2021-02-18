<?php

//////////////////////
// Common functions //
//////////////////////

function time2str($seconds) {
  $interval = (new DateTime('@0'))->diff(new DateTime("@0$seconds"));
  $p = function(&$f, $nb, $str) { if ($nb) $f[] = $nb.' '.($nb > 1 ?$str.'s':$str); };
  $f = array(); 
  $p($f, $interval->y, "year");
  $p($f, $interval->m, "month");
  $p($f, $interval->d, "day");
  $p($f, $interval->h, "hour");
  $p($f, $interval->i, "minute");
  $p($f, $interval->s, "second");
  return implode(" ", $f);
}

function check_drop_name($d) {
  return $d && preg_match("/^[a-zA-Z0-9]+$/", $d);
}

function auth_requiered($force=false) {
  global $CONFIG_AUTH;
  if (!$CONFIG_AUTH) return;

  $realm = 'ROOMS-drop';

  function askpass($realm) {
    header('HTTP/1.1 401 Unauthorized');
    header('WWW-Authenticate: Digest realm="'.$realm.'",qop="auth",nonce="'.uniqid().'",opaque="'.md5($realm).'"');
    die('Authentification failed');
  }
  
  function http_digest_parse($txt) {
      $needed_parts = array('nonce'=>1, 'nc'=>1, 'cnonce'=>1, 'qop'=>1, 'username'=>1, 'uri'=>1, 'response'=>1);
      $data = array();
      $keys = implode('|', array_keys($needed_parts));
      preg_match_all('@(' . $keys . ')=(?:([\'"])([^\2]+?)\2|([^\s,]+))@', $txt, $matches, PREG_SET_ORDER);
      foreach ($matches as $m) {
          $data[$m[1]] = $m[3] ? $m[3] : $m[4];
          unset($needed_parts[$m[1]]);
      }
      return $needed_parts ? false : $data;
  }

  if (empty($_SERVER['PHP_AUTH_DIGEST']) || $force) askpass($realm);

  if (!($data = http_digest_parse($_SERVER['PHP_AUTH_DIGEST'])) || !isset($CONFIG_AUTH[$data['username']]))
    askpass($realm);

  $A1 = $CONFIG_AUTH[$data['username']];
  $A2 = md5($_SERVER['REQUEST_METHOD'].':'.$data['uri']);
  $valid_response = md5($A1.':'.$data['nonce'].':'.$data['nc'].':'.$data['cnonce'].':'.$data['qop'].':'.$A2);

  if ($data['response'] != $valid_response)
    askpass($realm);
}

function remove_drop($d) {
  if (!check_drop_name($d)) return;
  array_map('unlink', glob("#uploads/$d/*"));
  rmdir("#uploads/$d");
}

function clean_uploads() {
  global $CONFIG_STORAGE_TIME;
  foreach (array_diff(scandir('#uploads/'), array('..', '.', '.empty')) as $d) {
    $tdiff = time() - intval(file_get_contents("#uploads/$d/date.txt"));
    if ($tdiff > $CONFIG_STORAGE_TIME)
      remove_drop($d);
  }
}

?>
