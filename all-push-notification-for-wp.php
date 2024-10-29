<?php
/*
Plugin Name: All Push Notifications for WordPress
Plugin URI: https://wordpress.org/plugins/all-push-notification/
Description: Send push notifications to iOS and Android when admin publish a new post/page and even new comment is added to any post. & Even from custom Editor from wp-admin. 
Version: 1.5.3
Author: GTL Developers (GTL)
*/
	require_once plugin_dir_path(__FILE__) . '/pushnotification-admin/class-pushnotification-admin.php';
	require_once plugin_dir_path(__FILE__) . '/pushnotification-admin/class-pushnotification-settings.php';
	require_once plugin_dir_path(__FILE__) . '/pushnotification-admin/class-custom-pushnotification.php';
	require_once plugin_dir_path(__FILE__) . '/pushnotification/class-pushnotification.php';
	require_once plugin_dir_path(__FILE__) . '/pushnotification/class-pushnotification-android.php';
	require_once plugin_dir_path(__FILE__) . '/pushnotification/class-pushnotification-ios.php';
	
	/*For posting data in table*/
	require_once plugin_dir_path(__FILE__) . '/customrest-api-url.php';

	/* Runs when plugin is activated */
	register_activation_hook(__FILE__,'all_pushnotification_forwp_install'); 

	/* Runs on plugin deactivation*/
	register_deactivation_hook( __FILE__, 'all_pushnotification_forwp_deactivation');

	/* Runs on plugin uninstall*/
	register_uninstall_hook( __FILE__, 'all_pushnotification_forwp_uninstall');

	// And here goes the activation function:
	function all_pushnotification_forwp_install() {
        
		global $wpdb;		
		$all_pushnotification_logs = $wpdb->prefix . 'all_pushnotification_logs';
		$all_pushnotification_token = $wpdb->prefix . 'all_pushnotification_token';		
		$charset_collate = $wpdb->get_charset_collate();
		$pushnotification_log_sql = "CREATE TABLE $all_pushnotification_logs (
			`log_id` int(11) NOT NULL AUTO_INCREMENT,
			`push_title` text NOT NULL,
			`push_message` text NOT NULL,
			`push_sent` tinyint(4) NOT NULL,
			`push_send_date` datetime NOT NULL,
			`devicetoken_id` text NOT NULL,
			PRIMARY KEY (`log_id`)
		) $charset_collate;";
		
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $pushnotification_log_sql );	

		$pushnotification_token_sql = "CREATE TABLE $all_pushnotification_token (
			`push_token_id` int(11) NOT NULL AUTO_INCREMENT,
			`device_token` text NOT NULL,
			`os_type` varchar(10) NOT NULL,
			`user_email_id` varchar(100) NOT NULL,
			`user_id` int(11) NOT NULL,
			`last_updatedate` datetime NOT NULL,
			PRIMARY KEY (`push_token_id`)
		) $charset_collate;";
		
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $pushnotification_token_sql );
		
		#Add Custom Upload folder in wp-upload folder
		$upload_dir = wp_upload_dir();
		$userdir = 'ioscerti';
		$user_dirname = $upload_dir['basedir'].'/'.$userdir;
		if ( ! file_exists( $user_dirname ) ) {
			wp_mkdir_p( $user_dirname );
		}
        
	}	
	
// And here goes the deactivation function:
	function all_pushnotification_forwp_deactivation(){
		global $wpdb;
		update_option( 'on_newpost_publish','');
		update_option( 'on_newpage_save','');
		update_option( 'on_newuser_register','');
		update_option( 'on_new_comment_post','');
		
		update_option( 'sendto_android','');
		update_option( 'sendto_ios','');
		update_option( 'send_via_production','');
		update_option( 'send_via_sandbox','');
		
		update_option( 'ios_certi_path','');
		update_option( 'ios_certi_name','');
		update_option( 'allpush_google_api_key','');
		update_option( 'send_to_android_via',''); 
	}
	
// And here goes the uninstallation function:
	function all_pushnotification_forwp_uninstall(){
		global $wpdb;
		$all_pushnotification_logs = $wpdb->prefix . 'all_pushnotification_logs';
		$all_pushnotification_token = $wpdb->prefix . 'all_pushnotification_token';	
		
		 //Delete table thats stored
		$wpdb->query("DROP TABLE IF EXISTS $all_pushnotification_logs");
		$wpdb->query("DROP TABLE IF EXISTS $all_pushnotification_token");
		
        //Delete any options thats stored
		delete_option('ios_certi_path');
		delete_option('ios_certi_name');
		delete_option('send_via_production');
		delete_option('send_via_sandbox');
		delete_option('on_newpost_publish');
		delete_option('on_newpage_save');
		delete_option('on_newuser_register');
		delete_option('on_new_comment_post');
		delete_option('sendto_android');
		delete_option('sendto_ios');
		delete_option('allpush_google_api_key');
		delete_option('send_to_android_via');
	}
		
	/* Call the Script code */
	add_action('admin_init', 'all_push_notification_scripts');	
	function all_push_notification_scripts() 
	{
		global $wpdb , $post;
		if (current_user_can('administrator')) 
		{
			wp_enqueue_script('jquery');
			wp_enqueue_script('validate',plugin_dir_url(__FILE__) .'js/jquery.validate.min.js', array(), '1.0.0', true );
			wp_enqueue_script('jquery-ui-selectmenu');
			wp_enqueue_script('pqselect.dev',plugin_dir_url(__FILE__) .'js/pqselect.dev.js', array(), '1.0.0', true );
			
			wp_enqueue_style('jquery-ui', plugin_dir_url(__FILE__) . 'css/jquery-ui.css' );
			wp_enqueue_style('custom', plugin_dir_url(__FILE__) . 'css/custom.css' );
			wp_enqueue_style('pqselect.dev', plugin_dir_url(__FILE__) . 'css/pqselect.dev.css' );
		}
	}

	 /* Call the menu code */
	add_action('admin_menu', 'all_pushnotification_forwp_admin_menu');
	function all_pushnotification_forwp_admin_menu() {
			global $wpdb , $post;
			if ( current_user_can('administrator') ){
				
				add_menu_page('All Push Notifications', 'All Push Notifications', 'administrator','all-Pushnotifications-wp', 'all_pushnotifications_wp_html');
				add_submenu_page('all-Pushnotifications-wp', 'Settings', 'Settings', 'administrator', 'all-PushNotifications-WP-settings','all_pushnotifications_wp_settings_html' );	
				add_submenu_page('all-Pushnotifications-wp', 'Custom Notification', 'Custom Notification', 'administrator', 'custom-PushNotifications-WP','custom_PushNotifications_wp_html' );	
			}
	   }
	   
	# For get all system users with is device token and device type.
	function all_push_notification_getAllSystemUsers() {
		
		global $wpdb; 
		$only_ios = get_option('sendto_ios');
		$only_android = get_option('sendto_android');

		$all_pushnotification_token = $wpdb->prefix . 'all_pushnotification_token';	
		$select_all_users = $wpdb->get_results("SELECT device_token,os_type FROM $all_pushnotification_token",ARRAY_A);		
		
		$all_userDevices = array();

		foreach ($select_all_users as $select_sql_data ) 
		{

			if(!empty($select_sql_data['os_type'])) { $deviceType = $select_sql_data['os_type']; } else {  $deviceType = ''; } 
			if(!empty($select_sql_data['device_token'])){ $deviceToken = $select_sql_data['device_token']; } else { $deviceToken = '';} 
			
			if ($deviceType == 'android' && $only_android =='yes') {
				array_push($all_userDevices, array('token' => $deviceToken, 'is_Android' => true));
			} elseif ($deviceType == 'ios' && $only_ios =='yes') {
				array_push($all_userDevices, array('token' => $deviceToken, 'is_Android' => false));
			}	
		}
		return $all_userDevices;
	}

	# For send notifications on post update/insert/edit.	
	add_action( 'publish_post', 'all_push_notification_save_post_page', 10, 2 );
	
	# For send notifications on page update/insert/edit.	
	add_action( 'publish_page', 'all_push_notification_save_post_page', 10, 2 );
	
	function all_push_notification_save_post_page()
	{	
		global $wpdb , $error; 
		$only_ios = get_option('sendto_ios');			 /* under settign page notification for android is seleted */
		$only_android = get_option('sendto_android');	/* under settign page notification for ios is seleted */
		$error = 'false';

		$post_title=sanitize_text_field($_POST['post_title']);
		$post_content=sanitize_text_field($_POST['post_content']);			
		$message = array("title" => $post_title,"message" => $post_content);
		$all_userDevices=all_push_notification_getAllSystemUsers();  /* user data will come from get all user Function */
		
							if($only_ios=='yes') {
								$ios_certi_name=get_option('ios_certi_name');
								if( (empty($ios_certi_name)) || (strlen($ios_certi_name) <= 0) ) {
									$error = 'true';
								}
							}
							
							if($only_android=='yes') {
								$allpush_google_api_key=get_option('allpush_google_api_key');
								if((empty($allpush_google_api_key)) || (strlen($allpush_google_api_key) <= 0)) {
									$error = 'true';
								}
							}
							
						if($error == 'false') {
								$PushNotifications_obj=new PushNotifications();
								$PushNotifications_obj->send_notification($all_userDevices,$message);
						}
							
						else{
								$error == 'true';								
								//echo 'For display error';
								//exit;							 
						  }  
						//echo  $error .'sds';
	}
	
	# For display error when notifications not send.	
	add_filter( 'post_updated_messages', 'all_push_notification_post_published' );
	function all_push_notification_post_published( $messages )
	{	
		global $post, $post_ID , $error;
		
		if(!empty($error)){
			
			$messages['post'][1]=sprintf( __('Post updated. <a href="%s">View post</a>  , notification not send Please check Setting page.'), 
			esc_url( get_permalink($post_ID) ) );
			$messages['post'][6]=sprintf( __('Post published. <a href="%s">View post</a>  , notification not send Please check Setting page.'), esc_url( get_permalink($post_ID) ) );
			
			$messages['page'][1]=sprintf( __('Page updated. <a href="%s">View page</a>  , notification not send Please check Setting page.'), 
			esc_url( get_permalink($post_ID) ) );
			$messages['page'][6]=sprintf( __('Page published. <a href="%s">View page</a>  , notification not send Please check Setting page.'), esc_url( get_permalink($post_ID) ) );
		}
		else {
			
			$messages['post'][1]=sprintf( __('Post updated. <a href="%s">View post</a>  ,  Notification Sent Successfully.'), 
			esc_url( get_permalink($post_ID) ) );
			$messages['post'][6]=sprintf( __('Post published. <a href="%s">View post</a>  , Notification Sent Successfully.'), 
			esc_url( get_permalink($post_ID) ) );
			
			$messages['page'][1]=sprintf( __('Page updated. <a href="%s">View page</a>  , Notification Sent Successfully.'), 
			esc_url( get_permalink($post_ID) ) );
			$messages['page'][6]=sprintf( __('Page published. <a href="%s">View page</a> , Notification Sent Successfully.'), 
			esc_url( get_permalink($post_ID) ) );
			
		}
		
		return $messages;
	}
	
	# For send notifications on Comment Post/Insert.
	add_action('wp_insert_comment','all_push_notification_comment_inserted',99,2);
	function all_push_notification_comment_inserted($comment_id, $comment_object) {
		
		global $wpdb;	
		global $post;
		$post_author_id = get_post_field ('post_author', $_POST['comment_post_ID']);		
		$post_title='Comment Inserted';		
		$PushNotifications_obj=new PushNotifications();		
		$message = array("title" => $post_title,"message" => $_POST['comment']);
		$all_pushnotification_token = $wpdb->prefix . 'all_pushnotification_token';	
		$post_author_Device=$wpdb->get_row("SELECT device_token,os_type FROM $all_pushnotification_token where user_id=".$post_author_id,ARRAY_A);

		if(!empty($post_author_Device['os_type'])) { $deviceType = $post_author_Device['os_type']; } else {  $deviceType = ''; } 
		if(!empty($post_author_Device['device_token'])){ $deviceToken = $post_author_Device['device_token']; } else { $deviceToken = '';}
		
		
		$all_userDevices = array();
		
		if ($deviceType == 'android' && $only_android =='yes') {
			array_push($all_userDevices, array('token' => $deviceToken, 'is_Android' => true));
		} elseif ($deviceType == 'ios' && $only_ios =='yes') {
			array_push($all_userDevices, array('token' => $deviceToken, 'is_Android' => false));
		}			
		$PushNotifications_obj->send_notification($all_userDevices,$message);
	}
	
	#Upload Certificate of Ios in custom directory of Upload folder:
	add_filter('upload_dir', 'all_push_notification_certificate_dir');
	function all_push_notification_certificate_dir( $param ){
		if(isset( $_SERVER['HTTP_REFERER'] )) {
			$request_uri_referrer = $_SERVER['HTTP_REFERER'];
			$request_uri_referrer_parts = explode( 'page=' , $request_uri_referrer );
            if(isset($request_uri_referrer_parts[1])) {
                $current_page_url=$request_uri_referrer_parts[1];
            
                if ($current_page_url=='all-PushNotifications-WP-settings') {
                    
                    $mydir = '/ioscerti';
                    $param['path'] = $param['basedir'] . $mydir;
                    $param['url'] = $param['baseurl'] . $mydir;
                
                }    
            }			
		}
		
		return $param;
		
	}
	
	#Upload Certificate Extention:
	function all_push_notification_push_mime_types($mime_types){
		$mime_types['pem'] = 'application/x-pem-file'; //Adding .pem extension
		$mime_types['p12'] = 'application/x-pkcs12';  //Adding photoshop files
		return $mime_types;
	}
	add_filter('upload_mimes', 'all_push_notification_push_mime_types', 1, 1);