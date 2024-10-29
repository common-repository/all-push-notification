<?php
add_action('rest_api_init', 'apn_register_wp_api_endpoints' );

function apn_register_wp_api_endpoints() {
	register_rest_route( 'apnwp', '/register', array(
        'methods' => 'GET',
        'callback' => 'apn_add_user_devicetoken',
    ));
}

function apn_add_user_devicetoken( $request_data ) {

	$parameters = $request_data->get_params();
	
	//print_r($parameters);

	if(!empty($parameters))
	{
		$user_name_array = explode('@',$parameters['user_email_id']);
		
		if(isset($user_name_array[0])) { $user_name = $user_name_array[0]; } else { $user_name = '' ;}
		
		if(isset($parameters['user_email_id'])) {$user_email_id=$parameters['user_email_id']; } else { $user_email_id = '' ;};
		
		if(isset($parameters['device_token'])) { $user_device_token=$parameters['device_token']; } else { $user_device_token = '' ;};
		
		if(isset($parameters['os_type'])) { $user_os_type=$parameters['os_type']; } else { $user_os_type = '' ;}
		
		$email_exists = email_exists($user_email_id);
		 
		if ( $email_exists == false ) {
			
			global $wpdb;		
			$random_password = wp_generate_password( $length=12, $include_standard_special_chars=false );
			$user_name = $user_name.mt_rand(10,100);
			$user_id = wp_create_user( $user_name, $random_password, $user_email_id );
			
			$last_updatedate=current_time( 'mysql' ); 
			$all_pushnotification_token = $wpdb->prefix . 'all_pushnotification_token';		
		
			
			$wpdb->insert($all_pushnotification_token,array('push_token_id' => null,'device_token' => $user_device_token,'os_type' => $user_os_type,'user_email_id' => $user_email_id,'user_id' => $user_id,'last_updatedate' => $last_updatedate),array('%d','%s','%s','%s','%d','%s')); 	
			
			//echo $wpdb->last_error;
			
			$error = "200";
			$errorMessage = "User successfully added in wpuser table"; 
			$jsonData = array("isError" => "false" ,"error" => $error, "SuccessMessage" => $errorMessage);
			$encodedData = json_encode($jsonData);
			echo $encodedData;
			exit;
			
		}

		else {
			
				global $wpdb;
				
				$last_updatedate=current_time( 'mysql' ); 
				$all_pushnotification_token = $wpdb->prefix . 'all_pushnotification_token';	
				
				$wpdb->update($all_pushnotification_token,array('device_token' => $user_device_token,'os_type' => $user_os_type,'last_updatedate' => $last_updatedate),array('user_email_id' => $user_email_id),array('%s','%s','%s'), array( '%s' ));
				
				//echo $wpdb->last_error;
			
				$error = "200";
				$errorMessage = "User successfully updated in all pushnotification token table"; 
				$jsonData = array("isError" => "false" ,"error" => $error, "SuccessMessage" => $errorMessage);
				$encodedData = json_encode($jsonData);
				echo $encodedData;
				exit;
		}	
}

	else {
				$error = "302";
				$errorMessage = "Please provide Proper Parameters"; 
				$jsonData = array("isError" => "true" ,"error" => $error, "errorMessage" => $errorMessage);
				$encodedData = json_encode($jsonData);
				echo $encodedData;
				exit;
	    }
}