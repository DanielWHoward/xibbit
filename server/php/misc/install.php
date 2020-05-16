<?php
///////////////////////////////////////////////////////
//                     xibbit 1.50                   //
//    This source code is a trade secret owned by    //
// Daniel W. Howard and Sanjana A. Joshi Partnership //
//              Do not remove this notice            //
///////////////////////////////////////////////////////
require_once('../config.php');
require_once('../pwd.php');
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>installation</title>
<style>
.warn {
  background-color: yellow;
}
.error {
  background-color: red;
}
</style>
</head>
<body>
<?php
// get the current time
date_default_timezone_set('America/Los_Angeles');
$now = date('Y-m-d H:i:s');
$nullDateTime = '1970-01-01 00:00:00';
$daysMap = array('SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT');
// connect to the database
$link = @mysqli_connect($sql_host, $sql_user, $sql_pass);
if (!$link) {
  print '<div class="error">'.$sql_host.' had a MySQL error ('.mysqli_errno().'): '.mysqli_error().'</div>'."\n";
}
// create the database
$q = 'CREATE DATABASE `'.$sql_db.'`';
$result = @mysqli_query($link, $q);
if (!$result) {
  if (mysqli_errno($link) == 1007) {
  } else {
    print '<div class="error">'.$sql_host.' had a MySQL error ('.mysqli_errno($link).'): '.mysqli_error($link).'</div>'."\n";
  }
}
// select the database
$result = mysqli_select_db($link, $sql_db);
if (!$result) {
  print '<div class="error">'.$sql_host.' had a MySQL error ('.mysqli_errno($link).'): '.mysqli_error($link).'</div>'."\n";
}
@mysqli_query($link, 'SET NAMES \'utf8\'');

// create the sockets table
$q = 'CREATE TABLE `'.$sql_prefix.'sockets` ( '
  .'`id` bigint(20) unsigned NOT NULL auto_increment,'
  .'`sid` text,'
  .'`connected` datetime NOT NULL,' // 2014-12-23 06:00:00 (PST)
  .'`touched` datetime NOT NULL,' // 2014-12-23 06:00:00 (PST)
  .'`props` text,'
  .'UNIQUE KEY `id` (`id`));';
if (!mysqli_query($link, $q)) {
  if (mysqli_errno($link) == 1050) {
    print '<div class="warn">Table '.$sql_prefix.'sockets already exists!</div>'."\n";
  } else {
    print '<div class="error">'.$sql_host.' had a MySQL error ('.mysqli_errno($link).'): '.mysqli_error($link).'</div>'."\n";
  }
} else {
  print '<div>'.$q.'</div>'."\n";
}

// create the sockets_events table
$q = 'CREATE TABLE `'.$sql_prefix.'sockets_events` ( '
  .'`id` bigint(20) unsigned NOT NULL auto_increment,'
  .'`sid` text,'
  .'`event` mediumtext,'
  .'`touched` datetime NOT NULL,' // 2014-12-23 06:00:00 (PST)
  .'UNIQUE KEY `id` (`id`));';
if (!mysqli_query($link, $q)) {
  if (mysqli_errno($link) == 1050) {
    print '<div class="warn">Table '.$sql_prefix.'sockets_events already exists!</div>'."\n";
  } else {
    print '<div class="error">'.$sql_host.' had a MySQL error ('.mysqli_errno($link).'): '.mysqli_error($link).'</div>'."\n";
  }
} else {
  print '<div>'.$q.'</div>'."\n";
}

// create the sockets_sessions table
$q = 'CREATE TABLE `'.$sql_prefix.'sockets_sessions` ( '
  .'`id` bigint(20) unsigned NOT NULL auto_increment,'
  .'`socksessid` varchar(25) NOT NULL,'
  .'`connected` datetime NOT NULL,' // 2014-12-23 06:00:00 (PST)
  .'`touched` datetime NOT NULL,' // 2014-12-23 06:00:00 (PST)
  .'`vars` text,'
  .'UNIQUE KEY `id` (`id`),'
  .'UNIQUE KEY `socksessid` (`socksessid`));';
if (!mysqli_query($link, $q)) {
  if (mysqli_errno($link) == 1050) {
    print '<div class="warn">Table '.$sql_prefix.'sockets_sessions already exists!</div>'."\n";
  } else {
    print '<div class="error">'.$sql_host.' had a MySQL error ('.mysqli_errno($link).'): '.mysqli_error($link).'</div>'."\n";
  }
} else {
  print '<div>'.$q.'</div>'."\n";
}
// add data to the sockets_sessions table
$q = 'SELECT id FROM '.$sql_prefix.'sockets_sessions';
$result = mysqli_query($link, $q);
if (mysqli_num_rows($result) === 0) {
  $values = array();
  $values[] = "0, 'global', '".$now."', '".$now."', '{}'";
  $values = isset($values_sockets_sessions)? $values_sockets_sessions: $values;
  foreach ($values as $value) {
    $q = 'INSERT INTO '.$sql_prefix.'sockets_sessions VALUES ('.$value.')';
    $result = mysqli_query($link, $q);
    if (!$result) {
      print '<div class="error">'.$sql_host.' had a MySQL error ('.mysqli_errno($link).'): '.mysqli_error($link).'</div>'."\n";
      print '<div class="error">'.$q.'</div>'."\n";
    } else {
      print '<div>'.$q.'</div>'."\n";
    }
  }
} else {
  print '<div class="warn">Table '.$sql_prefix.'sockets_sessions already has data!</div>'."\n";
}

// create the locks table
$q = 'CREATE TABLE `'.$sql_prefix.'locks` ( '
  .'`name` varchar(20),'
  .'`created` datetime NOT NULL,' // 2014-12-23 06:00:00 (PST)
  .'`json` text,'
  .'UNIQUE KEY `name` (`name`));';
if (!mysqli_query($link, $q)) {
  if (mysqli_errno($link) == 1050) {
    print '<div class="warn">Table '.$sql_prefix.'locks already exists!</div>'."\n";
  } else {
    print '<div class="error">'.$sql_host.' had a MySQL error ('.mysqli_errno($link).'): '.mysqli_error($link).'</div>'."\n";
  }
} else {
  print '<div>'.$q.'</div>'."\n";
}

// create the at table
$q = 'CREATE TABLE `'.$sql_prefix.'at` ( '
  .'`id` bigint(20) unsigned NOT NULL auto_increment,'
  .'`cmd` text,'
  .'`executed` datetime,' // 2014-12-23 06:00:00 (PST)
  .'`dow` varchar(3),'
  .'`elapsed` text,'
  .'UNIQUE KEY `id` (`id`));';
if (!mysqli_query($link, $q)) {
  if (mysqli_errno($link) == 1050) {
    print '<div class="warn">Table '.$sql_prefix.'at already exists!</div>'."\n";
  } else {
    print '<div class="error">'.$sql_host.' had a MySQL error ('.mysqli_errno($link).'): '.mysqli_error($link).'</div>'."\n";
  }
} else {
  print '<div>'.$q.'</div>'."\n";
}

// add data to the at table
$q = 'SELECT id FROM '.$sql_prefix.'at';
$result = mysqli_query($link, $q);
if (mysqli_num_rows($result) == 0) {
  $values = array();
  $values[] = "0, '#min hour day mon dow command', '".$now."', '".$daysMap[intval(date('w'))]."', ''";
  $values = isset($values_at)? $values_at: $values;
  foreach ($values as $value) {
    $q = 'INSERT INTO '.$sql_prefix.'at VALUES ('.$value.')';
    $result = mysqli_query($link, $q);
    if (!$result) {
      print '<div class="error">'.$sql_host.' had a MySQL error ('.mysqli_errno($link).'): '.mysqli_error($link).'</div>'."\n";
      print '<div class="error">'.$q.'</div>'."\n";
    } else {
      print '<div>'.$q.'</div>'."\n";
    }
  }
/*
  $q = 'ALTER TABLE '.$sql_prefix.'at  MODIFY COLUMN `id` bigint(20) unsigned AUTO_INCREMENT, AUTO_INCREMENT = 2;';
  $result = mysqli_query($link, $q);
  if (!$result) {
    print '<div class="error">'.$sql_host.' had a MySQL error ('.mysqli_errno($link).'): '.mysqli_error($link).'</div>'."\n";
    print '<div class="error">'.$q.'</div>'."\n";
  } else {
    print '<div>'.$q.'</div>'."\n";
  }
*/
} else {
  print '<div class="warn">Table '.$sql_prefix.'at already has data!</div>'."\n";
}

// create the users table
$q = 'CREATE TABLE `'.$sql_prefix.'users` ( '
  .'`id` bigint(20) unsigned NOT NULL auto_increment,'
  .'`uid` bigint(20) unsigned NOT NULL,'
  .'`username` text,'
  .'`email` text,'
  .'`pwd` text,'
  .'`created` datetime NOT NULL,' // 2014-12-23 06:00:00 (PST)
  .'`connected` datetime NOT NULL,' // 2014-12-23 06:00:00 (PST)
  .'`touched` datetime NOT NULL,' // 2014-12-23 06:00:00 (PST)
  .'`json` text,'
  .'UNIQUE KEY `id` (`id`));';
if (!mysqli_query($link, $q)) {
  if (mysqli_errno($link) == 1050) {
    print '<div class="warn">Table '.$sql_prefix.'users already exists!</div>'."\n";
  } else {
    print '<div class="error">'.$sql_host.' had a MySQL error ('.mysqli_errno($link).'): '.mysqli_error($link).'</div>'."\n";
  }
} else {
  print '<div>'.$q.'</div>'."\n";
}
// add data to the users table
$q = 'SELECT id FROM '.$sql_prefix.'users';
$result = mysqli_query($link, $q);
$dataInserted = false;
if (mysqli_num_rows($result) == 0) {
  $values = array();
  $values[] = "0, 1, 'admin', 'admin@xibbit.github.io', '".pwd_hash(hash('sha256', 'admin@xibbit.github.io'.'xibbit.github.io'.'passw0rd'))."', '".$now."', '".$nullDateTime."', '".$nullDateTime."', '{\"roles\":[\"admin\"]}'";
  $values[] = "0, 2, 'user1', 'user1@xibbit.github.io', '".pwd_hash(hash('sha256', 'user1@xibbit.github.io'.'xibbit.github.io'.'passw0rd'))."', '".$now."', '".$nullDateTime."', '".$nullDateTime."', '{}'";
  $values = isset($values_users)? $values_users: $values;
  foreach ($values as $value) {
    $q = 'INSERT INTO '.$sql_prefix.'users VALUES ('.$value.')';
    $result = mysqli_query($link, $q);
    if (!$result) {
      print '<div class="error">'.$sql_host.' had a MySQL error ('.mysqli_errno($link).'): '.mysqli_error($link).'</div>'."\n";
      print '<div class="error">'.$q.'</div>'."\n";
    } else {
      $dataInserted = true;
      print '<div>'.$q.'</div>'."\n";
    }
  }
/*
  $q = 'ALTER TABLE '.$sql_prefix.'users  MODIFY COLUMN `id` bigint(20) unsigned AUTO_INCREMENT, AUTO_INCREMENT = 9;';
  $result = mysqli_query($link, $q);
  if (!$result) {
    print '<div class="error">'.$sql_host.' had a MySQL error ('.mysqli_errno($link).'): '.mysqli_error($link).'</div>'."\n";
    print '<div class="error">'.$q.'</div>'."\n";
  } else {
    print '<div>'.$q.'</div>'."\n";
  }
*/
} else {
  print '<div class="warn">Table '.$sql_prefix.'users already has data!</div>'."\n";
}

// create the instances table
$q = 'CREATE TABLE `'.$sql_prefix.'instances` ( '
  .'`id` bigint(20) unsigned NOT NULL auto_increment,'
  .'`instance` text,'
  .'`connected` datetime NOT NULL,' // 2014-12-23 06:00:00 (PST)
  .'`touched` datetime NOT NULL,' // 2014-12-23 06:00:00 (PST)
  .'`sid` text,'
  .'`uid` bigint(20) unsigned NOT NULL,'
  .'`json` text,'
  .'UNIQUE KEY `id` (`id`));';
if (!mysqli_query($link, $q)) {
  if (mysqli_errno($link) == 1050) {
    print '<div class="warn">Table '.$sql_prefix.'instances already exists!</div>'."\n";
  } else {
    print '<div class="error">'.$sql_host.' had a MySQL error ('.mysqli_errno($link).'): '.mysqli_error($link).'</div>'."\n";
  }
} else {
  print '<div>'.$q.'</div>'."\n";
}

// close the database
if ($link) {
  mysqli_close($link);
}
?>
</body>
</html>
