<?php
	require_once plugin_dir_path(__FILE__).'/class-pushnotification-android.php';
	require_once plugin_dir_path(__FILE__).'/class-pushnotification-ios.php';

	class PushNotifications{
		
		function send_notification($userDevices, $message) {
			
			ini_set('max_execution_time', 600); //600 seconds = 10 minutes
			ini_set("memory_limit","512M");
			set_time_limit(0);
		
			/*for android pushnotification start*/
			$devices = array();
			foreach ($userDevices as $device) {
				
				if ($device['is_Android']){
					$devices[] = $device['token'];
				}
			}
			
			if (!empty($devices)){
				$PushNotifications_android_obj=new PushNotificationsAndroid();
				$PushNotifications_android_obj->sendToAndroid($devices, $message);
			}
			else{
				$android = 'No devices';
			}
			/*for android ends*/
					
			/*for ios pushnotification start*/
			$devices = array();
			foreach ($userDevices as $device) {
				if (!$device['is_Android'])
				$devices[] = $device['token'];
			}
			if (!empty($devices)){
				$PushNotifications_iOS_obj=new PushNotificationsIos();
				$PushNotifications_iOS_obj->sendToIOS($devices, $message);
			}
			else{
				$iOS = 'No devices';
			}
			
		}
	}
?>