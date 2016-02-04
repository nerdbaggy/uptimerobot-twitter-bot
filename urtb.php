<?php
require "config.php";
date_default_timezone_set($timezone);

require "twitteroauth/autoload.php";
use Abraham\TwitterOAuth\TwitterOAuth;


// Validate that the request is coming from UptimeRobot
if (verifyUptimeRobot() === false){
  error_log("Request not from UptimeRobot, dying");
  die();
}

// Easier naming conventions for when checks come from multiple sources
$alert = array(
  "host" => $_GET['monitorFriendlyName'],
  "id" => intval($_GET['monitorID']),
  "status" => $_GET['alertTypeFriendlyName'],
  "url" => $_GET['monitorURL'],
  "details" => $_GET['alertDetails'],
  "downtime" => 0,
  "timestamp" => date($timestampFormat),
  "timezone" => $timezone,
);

//If there isnt a usable ID just die
if ($alert['id'] === 0) {
  die();
}


// Load which hosts are currently down
$currentDown = getCurrentDown();

if ($alert['status'] === "Down"){
  // Make sure check isn't already down
  if (array_key_exists($alert['id'], $currentDown)) {
    error_log("Check got multiple down alerts, doing nothing");
    //die();
  }
  // Add the current down check
  $currentDown[$alert['id']] = time();

} elseif ($alert['status'] === "Up"){
  // Get downtime
  $alert['downtime'] = calcTimeDifference($currentDown[$alert['id']]);

  // Now that its up, delete the down record
  unset($currentDown[$alert['id']]);
}
// Write which hosts are currently down
writeCurrentDown($currentDown);

// Generate the message
$message = getMessage($alert);

// Post message to twitter
postTwitter($message);


/**
* Calculates how long the check was down for. I spent way to much time on this function
* @param  int $dt What time the check went down at in seconds since epoch
* @return string Properly formated time difference
*/
function calcTimeDifference($dt){
  // Used if there is no downtime record
  if (is_null($dt)){
    return "";
  }

  $downDt = new DateTime("@$dt");
  $nowDt = new DateTime();
  $day = $downDt->diff($nowDt)->format('%a');
  $hour = $downDt->diff($nowDt)->format('%h');
  $min = $downDt->diff($nowDt)->format('%i');
  $sec = $downDt->diff($nowDt)->format('%s');
  $downTime = "";

  if ($day == 1){
    $downTime .= "1 Day, ";
  } elseif ($day > 1){
    $downTime .= "$day Days, ";
  }

  if ($hour == 1){
    $downTime .= "1 Hour, ";
  } elseif ($hour > 1){
    $downTime .= "$hour Hours, ";
  }

  if ($min == 1){
    $downTime .= "1 Minute, ";
  } elseif ($min > 1){
    $downTime .= "$min Minutes, ";
  }

  if ($sec == 1){
    $downTime .= "1 Second";
  } elseif ($sec > 1){
    $downTime .= "$sec Seconds";
  }

  // Remove last , and space if present
  $downTime = rtrim($downTime, ', ');

  // Removes the last , and space and makes it into the word and
  $lastPos = strrpos($downTime, ', ');
  if ($lastPos != false){
    $downTime = substr_replace($downTime, " and ", $lastPos, 2);
  }

  return $downTime;
}

/**
* Posts the message to twitter
* @param  string $message The message to post to twitter
*/
function postTwitter($message){
  global $twitterConfig;
  $connection = new TwitterOAuth($twitterConfig['consumerKey'], $twitterConfig['consumerSecret'], $twitterConfig['accessToken'], $twitterConfig['accessTokenSecret']);
  $statues = $connection->post("statuses/update", ["status" => $message]);
  print_r($statues);
}

/**
* Creates a properly formated message
* @param  mixed[] $alert Current info about the alert called
* @return string Properly formated message
*/
function getMessage($alert){
  global $messages;

  $variableRep = array(
    'variable' => array('{{host}}', '{{id}}', '{{status}}', '{{url}}', '{{details}}', '{{downtime}}', '{{timestamp}}', '{{timezone}}'),
    'replacement' => array($alert['host'], $alert['id'], $alert['status'], $alert['url'], $alert['details'], $alert['downtime'], $alert['timestamp'], $alert['timezone']),
  );
  if ($alert['status'] === "Down"){
    return str_replace($variableRep['variable'], $variableRep['replacement'], $messages['downMessage']);
  } elseif ($alert['status'] === "Up") {
    if ($alert['downtime'] === ""){
      return str_replace($variableRep['variable'], $variableRep['replacement'], $messages['upMessageNoDown']);
    }
    return str_replace($variableRep['variable'], $variableRep['replacement'], $messages['upMessage']);
  }

}

/**
* Reads what checks are currently down from the file
* @return mixed[] $currentDown Array of checks and the time they went down
*/
function getCurrentDown(){
  global $cacheFile;
  $file = file_get_contents("./$cacheFile");
  if ($file === false){
    // Try and create file with empty array
    if (file_put_contents("./$cacheFile", serialize(array())) === false){
      error_log("Not able to write cache file. Manually create and make sure permissions are correct");
      die();
    }
    // Reget the file contents
    $file = file_get_contents("./$cacheFile");
  }
  return unserialize($file);
}

/**
* Writes which checks are currently down to a file
* @param mixed[] $currentDown Array of checks and the time they went down
*/
function writeCurrentDown($currentDown){
  global $cacheFile;
  if (file_put_contents("./$cacheFile", serialize($currentDown)) === false){
    error_log("Not able to write cache file. Manually create and make sure permissions are correct");
    die();
  }
}

/**
* Verifies that the request came from uptimeRobot
* @return bool true if the request came from uptime robot, false if it did not
*/
function verifyUptimeRobot(){
  global $debug, $uptimeRobotIps, $userAgentVerify;

  // Useful for local debugging
  if ($debug){
    return true;
  }

  // Make sure useragent string matches
  if (strpos($_SERVER['HTTP_USER_AGENT'], $userAgentVerify) === false){
    return false;
  }

  // Make sure IPs are from uptimerobot
  if (in_array($_SERVER['REMOTE_ADDR'], $uptimeRobotIps) === false){
    return false;
  }

  return true;
}
