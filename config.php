<?php
$twitterConfig = array(
  'consumerKey' => '',
  'consumerSecret' => '',
  'accessToken' => '',
  'accessTokenSecret' => '',
);

// You need to keep the timestamp in or Twitter will block messages
$messages = array(
  // Message to send once a check has come back online
  "upMessage" => "✔ {{host}} is now now up after {{downtime}} [{{timestamp}} {{timezone}}]",

  // Message to send if a check comes back online but never got a down notification
  "upMessageNoDown" => "✔ {{host}} is now now up [{{timestamp}} {{timezone}}]",

  // Message to send when a check has gone down
  "downMessage" => "✖ {{host}} has gone down [{{timestamp}} {{timezone}}]",
);

// Timestamp to display in the twitter message
// You can format it here: http://php.net/manual/en/function.date.php
$timestampFormat = 'h:i A';

// Timezone to display alerts in
// http://php.net/manual/en/timezones.php
$timezone = 'UTC';

// Name of the cache file
$cacheFile = 'twitterbot.cache';

// What useragent must be present in the request
$userAgentVerify = 'UptimeRobot';

// IPs to allow requests to come from.
// These are the engine IPs from https://uptimerobot.com/locations
$uptimeRobotIps = array(
  "69.162.124.226",
  "69.162.124.227",
  "69.162.124.228",
  "69.162.124.229",
  "69.162.124.230",
  "69.162.124.231",
  "69.162.124.232",
  "69.162.124.233",
  "69.162.124.234",
  "69.162.124.235",
  "69.162.124.236",
  "69.162.124.237",
  "69.162.124.238"
);

// Allows requests to come from non approved sources
// Don't use in production!!!!!!!!
$debug = false;
