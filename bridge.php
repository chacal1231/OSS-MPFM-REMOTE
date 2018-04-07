<?php
error_reporting(0);
$address = '0.0.0.0';
$port = 8000;
 
?>
 
<?php

set_time_limit(0);
ob_implicit_flush();
ignore_user_abort(true);
function socketError($errorFunction, $die=false) {
	$errMsg = socket_strerror(socket_last_error());
	
	if ($die) {
		die('</body></html>');
	}
}
if (!($server = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP))) {
	socketError('socket_create', true);
}
socket_set_option($server, SOL_SOCKET, SO_REUSEADDR, 1);
if (!@socket_bind($server, $address, $port)) {
	socketError('socket_bind', true);
}
if (!@socket_listen($server)) {
	socketError('socket_listen', true);
}
$allSockets = array($server);
while (true) {
    echo ' ';
    if (connection_aborted()) {
        	foreach ($allSockets as $socket) {
    		socket_close($socket);
		}
		break;
	}
	$changedSockets = $allSockets;
	socket_select($changedSockets, $write = NULL, $except = NULL, 5);
	foreach($changedSockets as $socket) {
	    if ($socket == $server) {
	        if (!($client = @socket_accept($server))) {
	        	socketError('socket_accept', false);
	        } else {
	        	$allSockets[] = $client;
	        }
	    } else {
	        $data = socket_read($socket, 2048);
	        if ($data === false || $data === '') {
	            unset($allSockets[array_search($socket, $allSockets)]);
	            socket_close($socket);
	        } else {
				if($data){
					foreach($allSockets as $m){
						if($m!=$socket){
						socket_send($m, $data, strlen($data),0);
						}
					}
				}
	        }
	    }
	}
} 
?>
 
</body>
