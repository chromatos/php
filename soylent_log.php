<?php
	error_reporting(E_ALL);
	include '.qcore.p'; # set password


	$result		= null;

	$operation	= strtolower($_GET['op']);
	$message	= $_GET['message'];
	$channel	= $_GET['channel'];
	$since_time	= $_GET['since'];
	$until_time	= $_GET['until'];
	$limit		= strtolower($_GET['limit']);
	$nick		= $_GET['nick'];
	$out		= strtolower($_GET['out']);
	$type		= strtolower($_GET['type']);
	$debug		= $_GET['debug'] != "";

	if ($out == "html")
		header('Content-Type: text/html; charset=utf-8');
	else
	{
		header('Content-Type: text/plain');
		if ($type == "") $type="default";
	}

	if ($operation == "source")
	{
		//print file_get_contents($argv[0]);
		print file_get_contents($_SERVER["SCRIPT_FILENAME"]);
		die;
	}

if ($debug) var_dump($_GET);

	if ($limit == "")
		$limit = "1";

	if ($operation == "")
	{	# print help:
		print "Ops: random, first, last, all, count, source. Parameters: message, nick, channel (regex); since, until (date-/time range); limit (maximum messages to return; default is 1, unless 
op=all); type [message (plain+action; default), plain, action, nick, mode, join, part, quit, kick, kill, topic]; out [json, php, tab, irc, message, html]; debug (show the query and whatever else)";
		die();
	}

	$connection = pg_connect("host=localhost dbname=qcore user=qcore password=".$the_secret_password)
		or die ('db connect failed: '. pg_last_error()."\n");

	$index = 0;

	$query = "select ";
	if ($operation == "count")
		$query .= "count(*)";
	else
		$query.= "*";

	$query .= " from combosearch where true ";

	if ($message != "")
	{	$query .= "and (message ~* $".++$index.") ";
		$query_parameters[] = $message;
	}
	
	if ($type != "")
	{	switch($type)
		{	case "message":
				$query.= "and (type = '1' or type = '4') ";
				break;
			case "plain":
				$query.= "and (type = '1') ";
				break;
			case "action":
				$query.= "and (type = '4') ";
				break;
			case "nick":
                                $query.= "and (type = '8') ";
				break;
			case "mode":
                                $query.= "and (type = '16') ";
				break;
			case "join":
                                $query.= "and (type = '32') ";
				break;
			case "part":
                                $query.= "and (type = '64') ";
				break;
			case "quit":
                                $query.= "and (type = '128') ";
				break;
			case "kick":
                                $query.= "and (type = '256') ";
				break;
			case "kill":
                                $query.= "and (type = '512') ";
				break;
			case "topic":
                                $query.= "and (type = '16384') ";
				break;
			default:
                                $query.= "and (type = '1' or type = '4') ";
		}
	}

	if ($nick != "")
	{	$query .= "and (sender ~* $".++$index.") ";
		$query_parameters[] = $nick;
	}

	if ($channel != "")
	{	$query .= "and (buffername ~* $".++$index.") ";
		$query_parameters[] = $channel;
	}

	if ($since_time != "")
	{	$query .= "and (time >= $".++$index.") ";
		$query_parameters[] = $since_time;
	}


if ($until_time != "")
	{	$query .= "and (time <= $".++$index.") ";
		$query_parameters[] = $until_time;
	}

	if ($operation == "random")
	{	$query.= "order by random() ";
	}
	else if ($operation != "count")
	{	$query .= "ORDER BY time "; 

		if ($operation == "last")
			$query .= "desc ";
	}

	if ($operation != "all" && $operation != "count")
	{	$query .= "LIMIT $".++$index." ";
		$query_parameters[] = $limit;
	}

	if ($debug)
	{	print "query: ".$query."\n\n";
		print "where\n";
		print_r($query_parameters);
		print "\n";
	}


	if (isset($query_parameters))
	{
		$prepared_query = pg_prepare($connection, "aQuery", $query)
			or die ('query preparation failed: '. pg_last_error()."\n");

		$result = pg_execute($connection, "aQuery", $query_parameters)
			or die('Query failed, faggot! '.pg_last_error()."\n");
	}
	else
	{
		$pg_query($connection, $query)
			or die('Query failed, faggot! '.pg_last_error()."\n");
	}

if ($operation == "count")
	$out = "tab";

if ($out == "html")
{	while ($line = pg_fetch_array($result, null, PGSQL_ASSOC))
	{
		$nick_end = strpos($line['sender'], "!");
		if ($nick_end === false)
			$nick_end = strlen($line['sender']);

		$is_action = $line['type'] == "4";

                $z = strpos($line['time'], ".");

               	if ($z === false)
       	                $new_time = $line['time'];
                else
               	        $new_time = substr($line['time'], 0, $z-1);
	
		$outputx = "<tr";
		if ($is_action)
			$outputx.= ' class="action"';

		$outputx.= '><td class="channel">'.$line['buffername'].'</td><td class="time" nowrap>'.$new_time."</td>";

		$vag = crc32($line['sender']) % 9;
		
		
		$outputx.= '<td class="nick nick-'. $vag.'">'.substr($line['sender'], 0, $nick_end).'</td>';

		$outputx.= '<td class="message">'.htmlspecialchars($line['message']).'</td></tr>';

		$output[] = $outputx;
	}
}


if ($out == "json")
{	while ($line = pg_fetch_array($result, null, PGSQL_ASSOC))
	{	$output[] = json_encode($line);
	}
}
else
if ($out == "php")
{	while ($line = pg_fetch_array($result, null, PGSQL_ASSOC))
	{	$output[] = serialize($line);
	}
}
else
if ($out == "tab")
{	while ($line = pg_fetch_array($result, null, PGSQL_ASSOC))
	{	$output[] = implode("\t", $line);
	}
}
else
if ($out == "message")
{	while ($line = pg_fetch_array($result, null, PGSQL_ASSOC))
	{	$output[] = $line['message'];
	}
}
else
{
	while ($line = pg_fetch_array($result, null, PGSQL_ASSOC))
	{	$nick_end = strpos($line['sender'], "!");
		if ($nick_end === false)
			$nick_end = strlen($line['sender']);

		$is_action = $line['type'] == "4";

		if ($out == "irc-full")
		{
	                $z = strpos($line['time'], ".");

                	if ($z === false)
        	                $new_time = $line['time'];
	                else
                	        $new_time = substr($line['time'], 0, $z-1);

	
			$outputx = "[".$new_time."] ".$line['buffername']." ";
		}
		else
			$outputx = "";

		if ($is_action)
			$outputx.= "-*- ";
		else
			$outputx.= "<";

		$outputx.= substr($line['sender'], 0, $nick_end);

		if (!$is_action)
			$outputx.=">";

		$outputx.= " ".$line['message'];

		$output[] = $outputx;
		
	}
}
pg_free_result($result);
pg_close($connection);


if (count($output) > 0)
{	if ($operation == "last")
		$output = array_reverse($output);


	if ($out == "html")
		print '<!DOCTYPE html">
<html><head><title>Log</title><link rel="stylesheet" type="text/css" href="soylent_log.css" /></head><body><table>';

	foreach ($output as $aLine)
		print $aLine."\n";

	if ($out == "html")
		print "</table></body></html>";
}
else
	print "[nothing found]";
?>
