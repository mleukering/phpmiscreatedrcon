<?php

	//written by Matthew Leukering (https://github.com/mleukering)(steam:twinturbodouche) on 02/18/2017

	function sendCmd($srvAddress, $srvPort, $srvPassword, $cmd2Send){

		//set our post URL to the server specified
		$url = 'http://' . $srvAddress . ":" . $srvPort . "/rpc2";

		//xml request to challenge authentication
		$xml = "<methodCall><methodName>challenge</methodName><params></params></methodCall>";

		//initilize curl like a bawssss
		$ch = curl_init(); 

		//let's set dem curl options.
		curl_setopt( $ch, CURLOPT_URL, $url); //url for the server's rcon server
		curl_setopt($ch, CURLOPT_POST, 1); //set curl to post
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true ); //we need data back so we've gotta set this bish
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml); //xml data to send to the rcon server
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml')); //we need to specify that we're sending xml data :)

		//execute the challenge command
		$result = curl_exec($ch);

		//get the server uptime(?) from the challenge command response with regex... this is kinda dirty
		preg_match_all('/<string>(.*?)<\/string>/', $result, $upTime);

		//build the challenge response getting the md5 of the uptime+:+password
		$challengeResponse = md5("{$upTime[1][0]}:{$srvPassword}");

		//gotta set the xml for the challenge response
		$xml = "<methodCall><methodName>authenticate</methodName><params><param><value><string>{$challengeResponse}</string></value></param></params></methodCall>";
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);

		//target down range... send it.
		$result = curl_exec($ch);

		//dont do anything with the auth response bcuz yolo we dun care.  We're just gonna sent the command because if auth failed, it's gonna fail this too.  No checks cause I'm a dickhead.  Cashmeousiii Howboudahhh

		//set the xml for the command we wanna send to rcon
		$xml = "<methodCall><methodName>{$cmd2Send}</methodName><params></params></methodCall>";
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);

		//omg finalllyyyyyyy
		$result = curl_exec($ch);

		//grab the useful bits from the response.
		preg_match_all('/<string>(.*?)<\/string>/s', $result, $cmdResults);

		//close connection
		curl_close($ch);

		//do something with this is you need to.  If you're just sending a command.  Fucking whatever.  Love you.
		return $cmdResults[1][0];

	}
