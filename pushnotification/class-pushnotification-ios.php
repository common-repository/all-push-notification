<?php	
class PushNotificationsIos {
	
	public static function sendToIOS($devices, $message) {	
		
		ini_set('max_execution_time', 600); //600 seconds = 10 minutes
		ini_set("memory_limit","512M");
		set_time_limit(0);
		
		$upload_dir = wp_upload_dir();
		$ios_certi_name=get_option('ios_certi_name');
		$user_iosdir = '/ioscerti/';
		$ios_certificate_custom_path=$upload_dir['basedir'].$user_iosdir.$ios_certi_name; /*for custom dir url*/
		$error = false;
		
		//post Option
		$msg_title= $message['title'];
		$message_text= $message['message'];
		
		if(empty($ios_certi_name) || strlen($ios_certi_name) <= 0) {
            $error = true;
            return $error;
        }
        $send_via_production=get_option('send_via_production');
		$ctx = stream_context_create();	 		
		stream_context_set_option($ctx, 'ssl', 'local_cert', $ios_certificate_custom_path);
		stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

		if($send_via_production=="yes")
		{
			$fp = stream_socket_client('ssl://gateway.push.apple.com:2195',$err,$errstr,60,STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT,$ctx);
		}
		else{
			$fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195',$err,$errstr,60,STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
		}
		
		if (!$fp){
			
			exit("Failed to connect: $err $errstr" . PHP_EOL);
		}
		else 
		{
			//
		}
		
		$results = array();		
		
		if(empty($msg_title)) {
			// Create the payload body
			$body['aps'] = array(
			'badge' => +1,
			'alert' => array(
			'title'=>'PushNotifications title to ios.',
			'body' =>'PushNotifications send to ios.',
			),
			'sound' => 'default'
			);
		}
		else {	
			// Create the payload body
			$body['aps'] = array(
			'badge' => +1,
			'alert' => array(
			'title'=>$message['title'],
			'body' =>$message['message'],
			),
			'sound' => 'default'
			);
		}
		
		$payload = json_encode($body);
		// Build the binary notification
		foreach ($devices as $device) {
			
			// Build the binary notification
			$msg = chr(0) . pack('n', 32) . pack('H*', $device) . pack('n', strlen($payload)) . $payload;
			
			//Send it to the server
			$results[] = fwrite($fp, $msg, strlen($msg));			
			
			//Insert msgs into pushnotification log table			
			global $wpdb;
			$blogtime = current_time( 'mysql' );
			$all_pushnotification_logs = $wpdb->prefix . 'all_pushnotification_logs';		
			$wpdb->insert($all_pushnotification_logs,array('push_title' => $msg_title,'push_message' => $message_text,'push_sent' => 1,'push_send_date' => $blogtime,'devicetoken_id' =>$device),array('%s','%s','%d','%s','%s'));			
		}
		
		if (empty($results)){
			//return ;
		}
		else {
			return $results;			
		}
		
		// Close the connection to the server
		fclose($fp);
        
        return $error;

	}
}
?>