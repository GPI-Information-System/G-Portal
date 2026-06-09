<?php
function getClientIP()
{
  if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ipList = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
    return trim($ipList[0]);
  }

  if (!empty($_SERVER['HTTP_X_REAL_IP'])) {
    return $_SERVER['HTTP_X_REAL_IP'];
  }

  return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}
