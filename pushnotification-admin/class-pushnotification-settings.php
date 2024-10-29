<?php
function all_pushnotifications_wp_settings_html(){
	global $wp;
	$current_url="//".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	$phpself_url=$_SERVER['PHP_SELF'];
?>
	<table>
		<tr>
			<td>
			<h2><?php _e('General Settings For Push Notifications')?></h2>
			</td>
		</tr>
	</table>	
	<form name="custom_pushnotification_optionsform" action="" id="custom_pushnotification_optionsform" method="post"> 	
	<?php	
	#first Section start
	global $wpdb;
	if(isset($_POST['save_options_setting_button']))
	{
		$on_newpost_publish = isset($_POST['on_newpost_publish']) ? $_POST['on_newpost_publish'] : "";
		
		$on_newpage_save = isset($_POST['on_newpage_save']) ? $_POST['on_newpage_save'] : "";
		
		if(($on_newpost_publish=="") && ($on_newpage_save=="")) {
			
			$message_er = __( 'Please select at least one checkbox box for send pushnotification.' );
			printf("<p class='error'>%s</p>", $message_er);
			
		}
		
		if (isset($_POST['on_newpost_publish'])) {
			$new_post = "yes";
		} else {
			$new_post = "no";
		}
		if (isset($_POST['on_newpage_save'])) {
			$new_page = "yes";
		} else {
			$new_page = "no";
		}
		if (isset($_POST['on_new_comment_post'])) {
			$new_comment = "yes";
		} else {
			$new_comment = "no";
		}
		if (isset($_POST['on_newuser_register'])) {
			$new_register = "yes";
		} else {
			$new_register = "no";
		}
		
		update_option( 'on_newpost_publish',$new_post);
		update_option( 'on_newpage_save',$new_page);
		update_option( 'on_new_comment_post',$new_comment);
		update_option( 'on_newuser_register',$new_register);
    }
    $on_newpost_publish=get_option('on_newpost_publish');
	$on_newpage_save=get_option('on_newpage_save');
	$on_newuser_register=get_option('on_newuser_register');
	$on_new_comment_post=get_option('on_new_comment_post');	
	 ?>
	 <table width="100%">
		<tr valign="top">
			<th width="15%" scope="row"> <h4 class="hndle"> <?php _e(' Send Push Notifications For :'); ?> </h4></th>
			<td>
			<input type="checkbox" name="on_newpost_publish" value="yes" <?php echo ($on_newpost_publish=='yes' ? 'checked' : '');?>> 
			<?php _e('When a new post is published'); ?>
			<br>
			<input type="checkbox" name="on_newpage_save" value="yes" <?php echo ($on_newpage_save=='yes' ? 'checked' : '');?>> 
			<?php _e('When a new page is published'); ?>
			<br>			
			</td>		  
		</tr>
		<tr>
			<td>
			<input type="submit" value="<?php _e('Save settings'); ?>" name="save_options_setting_button" class="button button-primary">
			</td>
		</tr>
	</table>
	</form>
	</br>
	
<form name="android_setting" action="" id="android_setting" method="post"> 
<?php 
	#Third Section start
	global $wpdb;
	wp_nonce_field( 'google_api_key', 'apn_gcm_key' );
	
	if(isset($_POST['save_android_setting']))
	{
		
		if (isset($_POST['sendto_android'])) {
			$sendto_android = "yes";
		} else {
			$sendto_android = "no";
		}
		
		update_option( 'sendto_android',$sendto_android);
		
		if(!wp_verify_nonce($_POST['apn_gcm_key'], 'google_api_key' )) { 
			
			 print 'Sorry, your nonce did not verify.';
			//exit;
		
		} else {
			
			$allpush_google_api_key=$_POST['allpush_google_api_key'];
			$send_to_android_via=$_POST['send_to_android_via'];
			update_option( 'allpush_google_api_key',$allpush_google_api_key);
			update_option( 'send_to_android_via',$send_to_android_via);
		}
		
	}
	
	$sendto_android=get_option('sendto_android');
    $allpush_google_api_key=get_option('allpush_google_api_key');
	$send_to_android_via=get_option('send_to_android_via');

	?>
<script>
	jQuery( document ).ready(function() {
		// validate signup form on keyup and submit
		jQuery("#android_setting").validate({
			rules: {
				allpush_google_api_key: "required",
				send_to_android_via: "required",
				sendto_android: "required"
			},
			messages: {
				allpush_google_api_key: "Please Enter Google Api Key",
				send_to_android_via: "Please Select option",
				sendto_android: "Please Select option for send notifications to android devices",

			},
			errorPlacement: function(error, element) {				
				jQuery(element).closest('tr').next().find('.error_label').html(error);
			}
		});
	});	
</script>
<table width="100%">
		<tr>
			<td colspan="2">
			<h4 class="hndle">
			<span><?php _e('Android Push Notifications (via Google Cloud Messaging, GCM / FCM)'); ?></span>
			</h4>
			</td>
		</tr>  
		<tr valign="top">
			<th width="15%" scope="row"> <h4> <?php _e('Send Push Notifications To :'); ?> </h4></th>
			<td>
			<input type="checkbox" name="sendto_android" value="yes" <?php echo ($sendto_android=='yes' ? 'checked' : '');?>> 
			<?php _e('Send Push notifications to Android devices'); ?>
			<br>
			</td>		  
		</tr>
		<tr>
			<td></td>
			<td><span class="error_label"></span></td>
		</tr>	
		<div class="auto_hide_android">
			<tr>
				<th width="15%" scope="row"> <h4> <?php _e(' Send Push Notifications via:'); ?>  </h4></th>
				<td>
				<input type="radio" name="send_to_android_via"
				<?php if (isset($send_to_android_via) && ($send_to_android_via=="gcm")) echo "checked";?> value="gcm">
				<?php _e('Send Push notifications to Android with GCM Key'); ?>
				<br>
				<input type="radio" name="send_to_android_via"
				<?php if (isset($send_to_android_via) && ($send_to_android_via=="fcm")) echo "checked";?> value="fcm">
				<?php _e('Send Push notifications to Android with FCM Key'); ?>
				</td>		  
			</tr>	
			<tr>
				<td></td>
				<td><span class="error_label"></span></td>
			</tr>		
			<tr valign="top">
				<th scope="row" width="15%"> <h4> <?php _e('Google API Key:'); ?> </h4></th>
				<td>
				<input type="text" maxlength="255" style="width:500px;" value="<?php echo $allpush_google_api_key; ?>" id="allpush_google_api_key" name="allpush_google_api_key" class="textfield" <?php if($sendto_android=='yes'){ echo 'required'; } ?> >
				<br>
				</td>		  
			</tr>
			<tr>
				<td></td>
				<td><span class="error_label"></span></td>
			</tr>
		</div>
	<tr>
	<td>
	<input type="submit" class="button button-primary" name="save_android_setting" id="save_android_setting" value="<?php _e('Save settings'); ?>">
	</td>
	</tr>	
	</table>
</form>

<form name="ios_setting" id="ios_setting" method="post" action="" enctype="multipart/form-data"> 
<?php 
	global $wpdb;	
	wp_enqueue_media();
	wp_enqueue_script('media-upload');
	wp_enqueue_script('thickbox');
	wp_enqueue_script('my-upload');
	wp_enqueue_style('thickbox');

	wp_nonce_field( 'ios_certi', 'apn_ios_certi' );
	
	if(isset($_POST['save_ios_settings_button']))
	{
		if (isset($_POST['sendto_ios'])) {
			$sendto_ios = "yes";
		} else {
			$sendto_ios = "no";
		}
		
		update_option( 'sendto_ios',$sendto_ios);
		
		if(!wp_verify_nonce($_POST['apn_ios_certi'], 'ios_certi' )) {
			
			 print 'Sorry, your nonce did not verify.';
			//exit;
		
		} else {
			
			$ios_certi_path=$_POST['upload_ios_certi'];
			$ios_certi_name=$_POST['upload_ios_certi_name'];
			
			$pushto_ios=$_POST['pushto_ios'];
			
			if ($pushto_ios=='send_via_production') {
				$send_via_production = "yes";
				$send_via_sandbox = "no";
			} else {
				$send_via_production = "no";
				$send_via_sandbox = "yes";
			}

			update_option( 'ios_certi_path',$ios_certi_path);
			update_option( 'ios_certi_name',$ios_certi_name);

			update_option( 'send_via_production',$send_via_production);
			update_option( 'send_via_sandbox',$send_via_sandbox);
		
		}
	}

	$sendto_ios=get_option('sendto_ios');
    $send_via_production=get_option('send_via_production');
	$send_via_sandbox=get_option('send_via_sandbox');
	$ios_certi_path=get_option('ios_certi_path');
	$ios_certi_name=get_option('ios_certi_name');	
?> 
<Script>
	jQuery(document).ready(function() {	
		// validate signup form on keyup and submit
		jQuery("#ios_setting").validate({
			rules: {
				upload_ios_certi: "required",
				pushto_ios: "required",
				sendto_ios: "required"
			},
			messages: {
				upload_ios_certi: "Please Upload Valid file",
				pushto_ios : "Please Select Option",
				sendto_ios: "Please Select option for send notifications to ios devices"
			},
			errorPlacement: function(error, element) {				
				jQuery(element).closest('tr').next().find('.error_label').html(error);
			}
		});
		
		// Uploading pem file
		var file_frame;
		jQuery('#upload_image_button').on('click', function( event ){
			event.preventDefault();

			// If the media frame already exists, reopen it.
			if ( file_frame ) {
				file_frame.open();
				return;
			}

			// Create the media frame.
			file_frame = wp.media.frames.file_frame = wp.media({
				title: jQuery( this ).data( 'uploader_title' ),
				button: {
				text: jQuery( this ).data( 'uploader_button_text' ),
				},
				multiple: false  // Set to true to allow multiple files to be selected
				});

			// When an image is selected, run a callback.
			file_frame.on( 'select', function() {
			  attachment = file_frame.state().get('selection').first().toJSON();
			  jQuery('#upload_ios_certi').val(attachment.url);
			  jQuery('#upload_ios_certi_name').val(attachment.filename);
			  
			});

			// Finally, open the modal
			file_frame.open();
		});

	});
</script>
	<table width="100%">
		<tr>
			<td colspan="2">
			<h4>
			<span><?php _e('iOS Push Notifications'); ?> <br> <?php _e('(Note :Use sandbox environment for Sandbox certificate (.pem File) OR Use Production environment for Production certificate. Notification will be sent through Apple Push Notification Service, APNs)'); ?> </span>
			</h4>
			</td>
		</tr>
		<tr valign="top">
			<th width="15%" scope="row"> <h4> <?php _e('Send Push Notifications To :'); ?> </h4></th>
			<td>
			<input type="checkbox" name="sendto_ios" value="yes" <?php echo ($sendto_ios=='yes' ? 'checked' : '');?>> 
			<?php _e('Send Push notifications to iOS devices'); ?>
			<br>
			</td>		  
		</tr>
		<tr>
			<td></td>
			<td><span class="error_label"></span></td>
		</tr>	
		<div class="auto_hide_ios">
			<tr>
				<th width="15%" scope="row"> <h4> <?php _e(' Send Push Notifications via:'); ?>  </h4></th>
				<td>
				<input type="radio" name="pushto_ios" value="send_via_sandbox" <?php echo ($send_via_sandbox=='yes' ? 'checked' : '');?>> 
				<?php _e('Send Push notifications to iOS devices using sandbox environment'); ?>
				<br>
				<input type="radio" name="pushto_ios" value="send_via_production" <?php echo ($send_via_production=='yes' ? 'checked' : '');?>> 
				<?php _e('send Push notifications to iOS devices using Production environment'); ?> <br>
				</td>		  
			</tr>	
			<tr>
				<th width="15%" scope="row"></th>
				<td><span class="error_label"></span></td>
			</tr>	
			<tr>
				<th width="15%" scope="row"> <h4><?php _e('Upload  certificate(.pem file)') ?>  </h4></th>
				<td>
				<label for="upload_ios_certi">
				<input id="upload_ios_certi" type="text" size="36" name="upload_ios_certi" value="<?php echo $ios_certi_path;?>" />
				<input id="upload_image_button" type="button" value="<?php _e('Upload  Certificate') ?>" data-uploader_title='Select .PEM File' data-uploader_button_text='Select' />
				<input type="hidden" name="upload_ios_certi_name" id="upload_ios_certi_name" value="<?php echo $ios_certi_name	;?>">
				</label>
				<br>
				</td>		  
			</tr>				
			<tr>
				<th width="15%" scope="row"></th>
				<td><span class="error_label"></span></td>
			</tr>
		</div>	
		<br>	
		<tr>
			<td>
			<input type="submit" value="<?php _e('Save settings'); ?>" name="save_ios_settings_button" class="button button-primary">
			</td>
		</tr>
	</table>	
	</form>
	<?php
}
?>