<?php
// prevent the server from timing out
set_time_limit(0);

// include the web sockets server script (the server is started at the far bottom of this file)
require 'class.PHPWebSocket.php';

// when a client sends data to the server
function wsOnMessage($clientID, $message, $messageLength, $binary) {
	global $Server;
	$ip = long2ip( $Server->wsClients[$clientID][6] );
	$Server->log($message);
	// check if message length is 0
	if ($messageLength == 0 || $message == "::close::") {
		$Server->wsClose($clientID);
		return;
	}
	if ($message == 'pingthread') {
        $Server->wsSend($clientID, "pingthread");
        return;
    }
	// client register logic
	
	if (!isset($Server->wsClients[$clientID][12]) || $Server->wsClients[$clientID][12] == '0') {
		$userinfos = explode(":wscommand:", $message);
		if (sizeof($userinfos) >= 2)
		{
			$userid = $userinfos[0];
			$channelid = $userinfos[1];
			if ($userid != '' && $channelid != '')
			{
				$Server->wsClients[$clientID][12] = '1';
				$Server->wsClients[$clientID][13] = $userid;
				$Server->wsClients[$clientID][14] = $channelid;
				foreach ( $Server->wsClients as $id => $client ) {
					if ( $id != $clientID && $client[12] == '1' && $client[14] == $Server->wsClients[$clientID][14])
						$Server->wsSend($id, "$userid joined!");
					/* else if ($id == $clientID)  
						$Server->wsSend($id, "$client[13] registered successfully!"); */
				}
				return;
			}
		} 
	}
	if (!isset($Server->wsClients[$clientID][12]))
		return;
	if ($Server->wsClients[$clientID][12] != '1') {
		// $Server->wsSend($clientID, "You must register.");
		return;
	} else {
		$isCommand = explode(":wscommand:", $message);
		if (sizeof($isCommand) >= 2)
			return;
		$userid = $Server->wsClients[$clientID][13];
		//The speaker is the only person in the room. Don't let them feel lonely.
		if ( sizeof($Server->wsClients) == 1 ) {
			 //$Server->wsSend($clientID, "There isn't anyone else in the room.");
		}
		else {
			//Send the message to everyone but the person who said it
			foreach ( $Server->wsClients as $id => $client ) {
				
				if ( $id != $clientID && $client[12] == '1' && $client[14] == $Server->wsClients[$clientID][14]) {
					$Server->wsSend($id, "$userid : \"$message\"");
					$Server->log("Send : ".$message);
				}
			}
		}
	}
}

// when a client connects
function wsOnOpen($clientID)
{
	global $Server;
	$ip = long2ip( $Server->wsClients[$clientID][6] );

	$Server->log( "$ip ($clientID) has connected." );
	
	$Server->wsClients[$clientID][12] = '0';
	$Server->wsClients[$clientID][13] = '';
	$Server->wsClients[$clientID][14] = '';	

	//Send a join notice to everyone but the person who joined
	/*foreach ( $Server->wsClients as $id => $client )
		if ( $id != $clientID )
			$Server->wsSend($id, "Visitor $clientID ($ip) has joined the room.");*/ 
}

// when a client closes or lost connection
function wsOnClose($clientID, $status) {
	global $Server;
	$ip = long2ip( $Server->wsClients[$clientID][6] );

	$Server->log( "$ip ($clientID) has disconnected." );
	
	if ($Server->wsClients[$clientID][12] != '1')
		return;
	$userid = $Server->wsClients[$clientID][13];
	//Send a user left notice to everyone in the room
	foreach ( $Server->wsClients as $id => $client ) {
		if ( $client[12] == '1' && $client[14] == $Server->wsClients[$clientID][14]) {			
			$Server->wsSend($id, "$userid has left the room.");
		}
	}
}

// start the server
$Server = new PHPWebSocket();
$Server->bind('message', 'wsOnMessage');
$Server->bind('open', 'wsOnOpen');
$Server->bind('close', 'wsOnClose');
// for other computers to connect, you will probably need to change this to your LAN IP or external IP,
// alternatively use: gethostbyaddr(gethostbyname($_SERVER['SERVER_NAME']))
$Server->wsStartServer('0.0.0.0', 9300);

?>