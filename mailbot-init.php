<?php

/*
 * PHP script to initialize needed tables for Mailbot service.
 * Written by Salvatore Carotenuto of OpenMakersItaly
 *
 * v. 1.0 - 2014-09-05
 */


// database parameters
$db_host = "your_host";
$db_user = "your_user";
$db_password = 'your_database_password"';
$db_name = "your_db_name";


// database connection
$db = mysql_connect($db_host, $db_user, $db_password) or die("Unable to connect to database: " . mysql_error());
mysql_select_db($db_name, $db);
echo "Database connection estabilished successfully\n<br>";


// creates 'users' table
//
$query = "CREATE TABLE IF NOT EXISTS `" . $db_name . "`.`mailbot_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(100) NOT NULL DEFAULT '',
  `user_email` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)) CHARACTER SET=utf8;";
// Execute query
if (mysql_query($query))
	echo "<br>Table 'mailbot_users' created successfully";
else
	echo "<br>Error creating table: " . mysql_error();


// creates 'groups' table
//
$query = "CREATE TABLE IF NOT EXISTS `" . $db_name . "`.`mailbot_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_name` varchar(30) NOT NULL DEFAULT '',
  `group_description` varchar(160) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)) CHARACTER SET=utf8;";
// Execute query
if (mysql_query($query))
	echo "<br>Table 'mailbot_groups' created successfully";
else
	echo "<br>Error creating table: " . mysql_error();


// creates 'groups_users' table
//
$query = "CREATE TABLE IF NOT EXISTS `" . $db_name . "`.`mailbot_groups_users` (
  `group_id` int(11) NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL DEFAULT 0) CHARACTER SET=utf8;";
// Execute query
if (mysql_query($query))
	echo "<br>Table 'mailbot_groups_users' created successfully";
else
	echo "<br>Error creating table: " . mysql_error();


// closes connection
mysql_close($db);
?>
