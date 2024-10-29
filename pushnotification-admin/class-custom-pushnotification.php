<?php
function custom_PushNotifications_wp_html(){
?>
<script>
jQuery(document).ready(function() {
		//initialize the pqSelect widget.
		jQuery("#selected_user").pqSelect({
				multiplePlaceholder: 'Select User',
				checkbox: true //adds checkbox to options    
		}).on("change", function(evt) {
			var val = jQuery(this).val();			
		}).pqSelect('close');			

		// validate signup form on keyup and submit
		jQuery("#custom_pushnotification_form").validate({
				ignore:'',
				rules: {
					'selected_user[]': {
					 required: true
				},
				message_text: {
				required: true,
				maxlength: 235
				},
				msg_title: "required",
				only_ios: {
				required: function() {
						if(jQuery("#only_android").prop('checked')) {
							return false;
						}
						return true;
					}
				},
				only_android: {
				required: function() {
						if(jQuery("#only_ios").prop('checked')) {
							return false;
						}
						return true;
					}
				}
				},
			messages: {
				'selected_user[]': "Please Select Users",
				msg_title: "Please enter your Message title",
				message_text: {
					required: "Please enter a Message",
					minlength: "Your Message Must not be more than 235 characters"
				}
			},
			errorPlacement: function(error, element) {			
				jQuery(element).closest('tr').next().find('.error_label').html(error);
			}
		});			
		
	});	
</script>
<form name="custom_pushnotification_form" action="" id="custom_pushnotification_form" method="post"> 
<table>
	<tr>
		<td>
		<h2><?php _e('Send Your Message via Push Notifications')?></h2>
		</td>
	</tr>	
	<p> </p>	
	<tr>
		<td cols="2">
		<h2><?php _e('What is Custom Push Notifications?');?> </h2>
		<p> <?php _e('- With this feature you can Send Your custom message via push notification to all or any specific WordPress users. It is importnat when you want to pass specific information to only limited user as per your requirement');?><p>
		
		<h2><?php _e('When to use Custom Push Notifications?');?></h2>
		<p> <?php _e('- When you want to Send push notification to WordPress users selectively. ');?><p>
		<p> <?php _e('- When you want to Send any custom message to any user directly from here. ');?><p>
		<p> <?php _e('- When you want to Send custom message to only ios mobile device user or only to android mobile users. ');?><p>
		
	    <h2><?php _e('How to send any custom message to any wp user?');?></h2>
		<p> <?php _e('- Just select the user login name from select box type your message title and text. select desired users ios / android device for notification. ');?><p>
		
			
		</td>
	</tr>
	<p> </p>
</table>

<table>
<?php
	global  $wp , $wpdb;
	
    require_once plugin_dir_path( dirname( __FILE__ ) ) . '/pushnotification/class-pushnotification.php';
	
	$current_url=$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; 
	$redirect_url='?page=all-Pushnotifications-wp';

	wp_nonce_field( 'custom_message', 'apn_custom_message' );
	
		
	$all_pushnotification_users = $wpdb->prefix . 'users';	
	$all_users = $wpdb->get_results("SELECT ID,user_login,user_nicename FROM $all_pushnotification_users");
	
	$error = false;
	$error_ios = false;
	$error_android = false;
    
	if(isset($_POST['send_now_button'])){
		
			if(isset($_POST['selected_user'])) { $selected_users_id = $_POST['selected_user']; } else {$selected_users_id = array();  }
			if(isset($_POST['only_ios'])) { $only_ios = $_POST['only_ios']; } else {$only_ios = '';}
			if(isset($_POST['only_android'])) { $only_android = $_POST['only_android']; } else {$only_android = ''; }
			
			$all_pushnotification_token = $wpdb->prefix . 'all_pushnotification_token';
			$all_device_tokens = $wpdb->get_results("SELECT device_token FROM $all_pushnotification_token");
			
			if(!wp_verify_nonce($_POST['apn_custom_message'], 'custom_message' )) {
				
					 print 'Sorry, your nonce did not verify.';
					//exit;
			
			}
			else{
					if( (!empty($selected_users_id)) && (!empty($all_device_tokens)) ){

							$all_userDevices = array();
							
							$message = array("message" => $_POST['message_text'],"title" => $_POST['msg_title']);

							$push_table_name = $wpdb->prefix . "all_pushnotification_token"; 

							foreach($selected_users_id as $selected_user_id)
							{	
								$user_data=$wpdb->get_row("SELECT device_token,os_type FROM `$push_table_name` where user_id=".$selected_user_id); 
								
								if(!empty($user_data->os_type)) { $deviceType = $user_data->os_type; } else {  $deviceType = ''; } 
								if(!empty($user_data->device_token)){ $deviceToken = $user_data->device_token; } else { $deviceToken = '';} 
								
								if ($deviceType == 'android' && $only_android !='') {
									array_push($all_userDevices, array('token' => $deviceToken, 'is_Android' => true));
								} elseif ($deviceType == 'ios' && $only_ios !='') {
									array_push($all_userDevices, array('token' => $deviceToken, 'is_Android' => false));
								}							
								
							}			

							if(!empty($only_ios)) {
								
								$ios_certi_name=get_option('ios_certi_name');
								if(empty($ios_certi_name) || strlen($ios_certi_name) <= 0) {
									$error_ios = true;
								}
							}
							
							if(!empty($only_android)) {
								
								$allpush_google_api_key=get_option('allpush_google_api_key');
								if(empty($allpush_google_api_key) || strlen($allpush_google_api_key) <= 0) {
									$error_android = true;
								}
							}
							
							if($error == false) {
								//$PushNotifications_obj=new PushNotifications();
								//$PushNotifications_obj->send_notification($all_userDevices,$message);
								
								$PushNotifications_obj=new PushNotifications();
								$regIdChunk=array_chunk($all_userDevices,100);
								
								foreach($regIdChunk as $RegId){
									
									 $PushNotifications_obj->send_notification($RegId,$message);
									 
								}
							}
						}
						else{
							
							$message = __( 'There was an error sending push notification message, There is no device tokens in table.' );
							printf("<p class='error'>%s</p>", $message);
						}
			}
	
			if($error_ios == true && $error_android == false)  {?>
			<tr>
				<td colspan="2">
				<?php 
					$message = __( 'There was an error sending push notification message to ios mobile devices, please check Settings page and verify PEM certificate file for APNs.' );
					printf("<p class='error'>%s</p>", $message);
				?>
				</td>
			</tr>
			<?php } else if( $error_android == true && $error_ios == false ) {?>
			<tr>
				<td colspan="2">
				<?php 
					$message = __( 'There was an error sending push notification message to android mobile devices, please check Settings page and verify GCM / FCM Key.' );
					printf("<p class='error'>%s</p>", $message);
				?>
				</td>
			</tr>
			<?php } else if( $error_android == true && $error_ios == true ) { ?>
			<tr>
				<td colspan="2">
				<?php 
					$message = __( 'There was an error sending push notification message to mobile devices, please check Settings page and verify PEM certificate file for APNs and also verify GCM / FCM Key.' );
					printf("<p class='error'>%s</p>", $message);
				?>
				</td>
			</tr>
		<?php 
			} 
			else { ?>
		<tr>
			<td colspan="2">
			<?php 
				$message = __( 'The messgae send successfully. please check the pushntofication in device.' );
				printf("<p class='suceess' style='color:green'>%s</p>", $message);
			?>
			</td>
		</tr>
		<?php 
				} 

		} /*post end*/	
		
		?>
	<tr>
		<td><?php _e('Select Users')?>:</td>
		<td>
			<select id="selected_user" name="selected_user[]" multiple=multiple style="margin: 20px;width:300px;" required>    
			<?php
			foreach ( $all_users as $user ) 
			{
				echo '<option value='.$user->ID.'>'.$user->user_nicename.'</option>';
			}
			?>					
			</select>
		</td>
	</tr>	
	<tr>
		<td></td>
		<td><span class="error_label"></span></td>
	</tr>	
	<tr>
		<td><?php _e('Message Title')?>: </td>
		<td><input type="text" name="msg_title" required></td>
	</tr>	
	<tr>
		<td></td>
		<td><span class="error_label"></span></td>
	</tr>	
	<tr>
		<td><?php _e('Message Text')?>: </td>
		<td><textarea rows="5" cols="25" name="message_text" required></textarea></td>
	</tr>	
	<tr>
		<td></td>
		<td><span class="error_label"></span></td>
	</tr>
	<tr>
		<td><?php _e('Send Push Notifications To')?>:</td>		
		<td>
		<input type="checkbox" name="only_ios" value="yes" id="only_ios" class="checkBox_class"> 
		<label for="only_ios"><?php _e('iOS devices')?></label>
		<br>		   
		<input type="checkbox" name="only_android" value="yes" id="only_android" class="checkBox_class"> 
		<label for="only_android"><?php _e('Android devices')?></label><br>
		</td>
	</tr>	
	<tr>
		<td></td>
		<td><span class="error_label"></span></td>
	</tr>
	<tr>
		<td></td>
		<td><span><?php _e('Note: Please make sure you have done valid setting under "All Settings" menu.')?></span></td>
	</tr>
	<tr>
		<td>
		<input type="submit" value="<?php _e('Send Now')?>" name="send_now_button" id="send_now_button" class="button button-primary">
		</td>
	</tr>
	</table>
	<br>
	<br>
	</form>
	<?php
}
?>