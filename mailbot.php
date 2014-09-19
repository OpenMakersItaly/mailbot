<?php

/*
 * Mailbot: PHP script to fetch and forward emails.
 * Written by Andrea Pastore and Salvatore Carotenuto of OpenMakersItaly
 *
 * v. 1.0 - 2014-09-05
 * v. 1.1 - 2014-09-06 Modified mail header and body msg (Daniele Costarella)
 */



// mail server parameters
$pop3="your_pop3_server";
$server = 'your_Server';
$username="your_username";
$password="your_mail_server_password";


// connects to MySql database
function dbConnect()
	{
	// database parameters
	$db_host = "your_host";
	$db_user = "your_user";
	$db_password = 'your_database_password"';
	$db_name = "your_db_name";
	// database connection
	$db = mysql_connect($db_host, $db_user, $db_password) or die("Unable to connect to database: " . mysql_error());
	mysql_select_db($db_name, $db) or die("Unable to select to database: " . mysql_error());
	}


function fetchGroupIdByName($groupName)
	{
	// gets allowed group id
	$groupId = 0;
	$data = mysql_query("SELECT id FROM mailbot_groups WHERE group_name = '".$groupName."';") or die(mysql_error());
	$num_rows = mysql_num_rows($data);
	if($num_rows == 1)
		{
		$results = mysql_fetch_array( $data );
		$groupId = $results['id'];
		}
	//
	return $groupId;
	}
	
	
// this function checks if an email address is allowed to forward emails
function checkAllowedSender($mailAddress)
	{
	$num_rows = 0;
	$allowed_group_id = fetchGroupIdByName("ALLOWED_SENDERS");
	if($allowed_group_id != 0)
		{
		// checks if given email address is in allowed group
		$data = mysql_query("SELECT * FROM mailbot_groups_users AS gu JOIN mailbot_users AS u on gu.user_id = u.id WHERE gu.group_id = ".$allowed_group_id." AND u.user_email = '".$mailAddress."';") or die(mysql_error());
		$num_rows = mysql_num_rows($data);
		}
	//
	return $num_rows == 1;
	}


// this function gets recipients email by group name
function getRecipients($groupName)
	{
	$recipients = Array();
	if($groupName == 'ALL')
		{
		$data = mysql_query("SELECT * FROM mailbot_users") or die(mysql_error());
		while($results = mysql_fetch_array( $data ))
			{
			$recipients[] = $results['user_email'];
			}
		}
	else
		{
		$groupId = fetchGroupIdByName($groupName);
		$data = mysql_query("SELECT * FROM mailbot_groups_users AS gu JOIN mailbot_users AS u on gu.user_id = u.id WHERE gu.group_id = ".$groupId.";") or die(mysql_error());
		while($results = mysql_fetch_array( $data ))
			{
			$recipients[] = $results['user_email'];
			}
		}
	//
	return $recipients;
	}
	

// main script
dbConnect();
if($inbox=@imap_open($server, $username, $password))
	{ 
	echo "mailbox opened<br>";
	// reads messages number
	$totalMessages = imap_num_msg($inbox);
	echo "<b>Total messages:</b> $totalMessages<br/><br/>";
	
	if($totalMessages > 0)
		{
		// browses all messages
		for($m = $totalMessages; $m>0; $m--)
			{
			Print("Processing message ".$m." ----------------------------------<br>");
			$headers  = imap_headerinfo($inbox, $m);
			$sender = $headers->from[0]->mailbox . "@" . $headers->from[0]->host;
			
			// checks if sender is allowed to forward messages
			if(checkAllowedSender($sender))
				{
				//printf("mittente: %s<br><br>", $mittente);
				/*foreach($headers as $key) {
					printf("key: %s<br>", $key);
				}*/
				$subject = utf8_decode(imap_utf8($headers->subject));
				
				// extracts recipients rule from subject
				$ruleEndPos = strpos($subject, "]");
				$rule = "";
				if ($ruleEndPos !== false && $ruleEndPos > 1)
					{
					$rule = substr($subject, 0, $ruleEndPos+1);
					// trims white spaces
					$rule = trim($rule);
					// trims white spaces
					$rule = trim($rule, "[]");
					//
					// trims rule prefix
					$subject = substr($subject, $ruleEndPos+1);
					// trims white spaces
					$subject = trim($subject);
					}
				Print("rule: ".$rule."<br>");
				Print("subject: ".$subject."<br><br>");

				//$body = imap_body($inbox, $m);
				$body = imap_fetchbody($inbox, $m, 2);
				if ($body == "")
					{
					Print("<b>unable to extract html body part!</b><br>");
					$body = imap_fetchbody($inbox, $m, 1);
					}
					
				$body = trim(quoted_printable_decode($body));
				
				$body = $body.'<br>Inviato da: '.$sender.'<br>';
				
				
				/*$body = imap_fetchbody($inbox, $m, "1.2");
				if(strlen($body) == 0)
					$body = imap_fetchbody($inbox, $m, "1.1");*/
				Print("body: ".$body."<br>");

				$recipients = getRecipients($rule);
				foreach($recipients as $recipient)
					{
					$headers = "From: Open Makers Italy <mailbot@openmakersitaly.org>\r\n"; 
					/* $headers  = "From: $sender <$sender>\r\n"; */
					$headers .= "MIME-Version: 1.0\r\n";
					$headers .= "Content-type: text/html; charset=UTF-8\r\n";
					$headers .= "X-Mailer: PHP/" . phpversion() ."\r\n";

					//$email = $recipient;
					//$messageBody = "$testo";
					mail($recipient, $subject, $body, $headers);
					printf("recipient: %s<br>subject: %s<br>body: %s<br>headers: %s<br><br>", $recipient, $subject, $body, $headers);
					}
				}
			// marks processed message as deleted
			imap_delete($inbox, $m);
			}
		}
	imap_close($inbox, CL_EXPUNGE);
	}
else
	{
	echo "unable to open mailbox<br>";
	echo imap_last_error();
	}

// closes database connection
mysql_close($db);
?>
