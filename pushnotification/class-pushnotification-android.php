<?php
class PushNotificationsAndroid {
	
	function sendToAndroid($registatoin_ids, $message) {
		
		ini_set('max_execution_time', 600); //600 seconds = 10 minutes
		ini_set("memory_limit","512M");
		set_time_limit(0);
			
        $error = false;
		
		//post Option
		$msg_title= $message['title'];
		$message_text= $message['message'];

		//Get Option		
		$allpush_google_api_key=get_option('allpush_google_api_key');
        if(empty($allpush_google_api_key) || strlen($allpush_google_api_key) <= 0) {
            $error = true;
            return $error;
        }
			
		//Get Option		
		$allpush_send_to_android_via=get_option('send_to_android_via');
		
		// include config
		define('GOOGLE_API_KEY',$allpush_google_api_key);				
		
		// Set POST variables 
		if($allpush_send_to_android_via=='gcm') {
			
				$url = 'https://android.googleapis.com/gcm/send';
				
		}
		else {
			
				$url = 'https://fcm.googleapis.com/fcm/send';
		}
		
		if(empty($msg_title)) {
			// prep the bundle
			$message = array
			(
				'message' 	=> 'PushNotifications send to android.',
				'title'		=> 'PushNotifications title to android.',
				'subtitle'	=> 'PushNotifications subtitle to android.',
				'tickerText'=> 'PushNotifications Ticker text here...',
				'vibrate'	=> 1,
				'sound'		=> 1,
				'largeIcon'	=> 'large_icon',
				'smallIcon'	=> 'small_icon'
			);
		}
		else {
			
			$message = array
			(
				'message' 	=> $message_text,
				'title'		=> $msg_title,
				'vibrate'	=> 1,
				'sound'		=> 1,
				'largeIcon'	=> 'large_icon',
				'smallIcon'	=> 'small_icon'
			);
			
		}

		$fields = array(
		'registration_ids' => $registatoin_ids,
		'data' => $message,
		);
		
		$headers = array(
		'Authorization: key=' . GOOGLE_API_KEY,
		'Content-Type: application/json'
		);
		// Open connection
		$ch = curl_init();
		
		// Set the url, number of POST vars, POST data
		curl_setopt($ch, CURLOPT_URL, $url);		
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		// Disabling SSL Certificate support temporarly
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);		
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));		
		// Execute post
		$result = curl_exec($ch);
		if ($result === FALSE) {
			die('Curl failed: ' . curl_error($ch));
		}		
		
		//echo $result; //check the result

		global $wpdb;		
		$blogtime = current_time( 'mysql' );		
		$all_pushnotification_logs = $wpdb->prefix . 'all_pushnotification_logs';
		foreach ( $registatoin_ids as $registatoin_id )
		{
			$wpdb->insert($all_pushnotification_logs,array('push_title' => $msg_title,'push_message' => $message_text,'push_sent' => 1,'push_send_date' => $blogtime,'devicetoken_id' =>$registatoin_id),array('%s','%s','%d','%s','%s'));
			//$wpdb->print_error();
			
		}
						
		// Close connection
		curl_close($ch);
        return $error;
		
	}
}
?>