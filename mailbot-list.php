 
<?php

/*
 * PHP script to list current users settings for Mailbot service.
 * Written by Salvatore Carotenuto of OpenMakersItaly
 *
 * v. 1.0 - 2014-09-05
 */


// database parameters
$db_host = "your_host";
$db_user = "your_user";
$db_password = 'your_database_password"';
$db_name = "your_db_name";

echo "<h1>OpenMakers' Mailbot Service</h1>";
echo "<h2>Current users settings</h2><br>";

// database connection
$db = mysql_connect($db_host, $db_user, $db_password) or die("Unable to connect to database: " . mysql_error());
mysql_select_db($db_name, $db);
echo "<br>Database connection estabilished successfully<br>";

echo "<h3>Users list:</h3>";
$data = mysql_query("SELECT * FROM mailbot_users") or die(mysql_error());
Print "<table style=\"border: 1px solid grey;\">";
Print "<tr><th>id:</th><th>Name:</th><th>email:</th></tr>";
while($results = mysql_fetch_array( $data ))
	{
	Print "<tr>";
	Print "<td>" . $results['id'] . "</td> ";
	Print "<td>".$results['user_name'] . "</td> ";
	Print "<td>".$results['user_email'] . "</td> ";
	Print "</tr>";
	}
Print "</table>";

echo "<h3>Groups:</h3>";
$data = mysql_query("SELECT * FROM mailbot_groups") or die(mysql_error());
Print "<table style=\"border: 1px solid grey;\">";
Print "<tr><th>id:</th><th>Name:</th><th>Description:</th></tr>";
$groups = array();
while($results = mysql_fetch_array( $data ))
	{
	$groups[$results['group_name']] = $results['id'];
	Print "<tr>";
	Print "<td>" . $results['id'] . "</td> ";
	Print "<td>".$results['group_name'] . "</td> ";
	Print "<td>".$results['group_description'] . "</td> ";
	Print "</tr>";
	}
Print "</table>";

Print "<h3>Groups members:</h3>";
foreach ($groups as $key => $value)
	{
	Print "<b>".$key.":</b>";
	$data = mysql_query("SELECT * FROM mailbot_groups_users AS gu JOIN mailbot_users AS u on gu.user_id = u.id WHERE gu.group_id = ".$value.";") or die(mysql_error());
	Print "<table style=\"border: 1px solid grey;\">";
	Print "<tr><th>id:</th><th>Name:</th><th>email:</th></tr>";
	while($results = mysql_fetch_array( $data ))
		{
		Print "<tr>";
		Print "<td>" . $results['id'] . "</td> ";
		Print "<td>".$results['user_name'] . "</td> ";
		Print "<td>".$results['user_email'] . "</td> ";
		Print "</tr>";
		}
	Print "</table>";
	}
	
mysql_close($db);
?>
