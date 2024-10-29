=== All push notification for WP ===
Contributors: gtlwpdev
Tags: push, notification, mobile, android, ios, app, push notification , Google Cloud Messaging , GCM , gcm , Firebase Cloud Messaging , FCM , fcm , Android , android , ios , IOS , New post page , New comment , Post published , Send Custom Message To WP User
Requires at least: 4.4
Tested up to: 4.7
Stable tag: 1.5.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html


Send push notifications to iOS and Android devices for free, no separate third-party server integration required.

== Description ==

All push notifications for WP is useful plugin to send push notification to iOS and Android devices using WordPress integration.User can send push notifications to iOS and Android with custom Editor from wp-admin and even When admin publish a new post/page and even new comment is added to any post.

= Key Features =
All push notifications for WP supports following environments:

* Apple Push Notification service (APNs)
* Google Cloud Messaging (GCM)
* Firebase Cloud Messaging (FCM)

By using this plugin you can:

1. Send push notification to WordPress users selectively.
2. Send push notification to users when new post is published or when new comment is added to the post (administrator user)

= Important note =

To send push notification to android devices, you will need API Key from Google GCM platform.

To send push notification to ios devices, you will need pem certification file from Apple APNs.

For registering any device / user for push noficiation, it is required to integrate 'register' api in mobile application. Register API is part of the plugin only i.e. it is not third party API. It also means that all device token data is maintained in your wordpress application database only. Its basic use is to gather device token of respective iOS and Android devices so that push notifications can be sent from wordpress. More details about the same has been given in FAQ section.

From the device app, it is required to send additional headers information so that device is recognized by Google Cloud Messaging (GCM) and Apple Push Notification service (APNs)

Following headers are required:

1. `device_token`: It will be the unique identifier provided by operating system to device.
2. `os_type`: The type of device os - Use `android` for Android and `ios` for iOS devices.


== Installation ==

= Minimum Requirements =

* WordPress 4.4 or greater
* PHP version 5.2.4 or greater
* MySQL version 5.0 or greater

1. Download "All push notification for WP" as zip file from the plugin download link.
2. Extract the zip file and upload 'all-push-notifications-for-wp' to `/wp-content/plugins/` directory
3. Activate the plugin from admin panel plugin listing screen.
4. You can now configure plugin from setting screen which appears in admin menu.

== Frequently Asked Questions ==

Que) when notification will send? 

Ans - Notification will send to all the users default when new post is published by wp admin . 
	- Notification will send to all the users default when new page is created.

Que) what is the URL I have to make a request to once I have the plugin installed? How to pass os_type (operating system type [android / ios]) and device_token (mobile unique token) parameters in plugin. ?

Ans - The os_type and device_token parameters need to be passed as Request Parameters in register api suggested below. The API will work like if user is already register in wp_user table the user with device token and device os(operating system) type will be registered. If the user is already registered then the device token and device os type will be updated.

	To send a push notification to a device, we must know its token (or Device ID) which has to be provided by the app through this API. This API allows client device to register itself to Push Notifications for WordPress so that it can receive future notifications.

	URL structure:

	http://yourwordpresssite/wp-json/apnwp/register

	Method:  GET

	Parameters:
	device_token (string): token given by APNs or FCM identifying the device, often called device ID.
	os_type (string): operating system. It must be ios or android (case sensitive).
	user_email_id (string, optional): the user email address which is login from your mobile app.
	
	Examples:
	
	http://yourwordpresssite/wp-json/apnwp/register?os_type=android&user_email_id=androidmobile@40test.com&device_token=1234567890

	Returns:

	200: on success.Either user will be add / updated.
	302: Invalid mandatory parameters passed.
	500: Internal Server Error , on missing mandatory parameters, unknown operating system, or general failure.


Que) I have  install all-push-notification plugin to send notification in ios and android .But I do not know that what I do next. Front end developer open the website in web view then I have to send the push notification for that.don not know that how to add the device id and type in the database and how to send the notification for wp-admin? . Can you please tell me what will be URL to send device token at the time of register ? .In my app, I am not login or registering on site.

Ans - If you do not  have the application or do not make user register or you run your website through the web view or direct web URL in your mobile devices then the notification will not work. 

Que)  Even if I checked those box and save setting,but I still not received notification when I add new post.I checked all of the [Send Push Notifications For] box group and Send Push notifications to Android devices of [Send Push Notifications To] group,my environment is wp V4.6.1 , use FCM service.

Ans - Please refer Arbitrary section.

Que)  If I m usign lower version then 4.4 then?

Ans - You need to download this plugin for API. https://wordpress.org/plugins/rest-api/ . Then followt the steps as per the Arbitrary section.
	

 ** Do you have questions or issues with All push notification for WP? You can send them to 
 <a href="mailto:gtl.wpdev@gmail.com">gtl.wpdev@gmail.com</a>

If you are using the plugin and found it to be useful in your projects, We would urge you to give us rating and review, it will help us making plugin more effective with future releases.

== Screenshots ==

1. Admin Settings screen.
2. Admin Send push notification manually.
3. List of Sent Items in Admin Panel.

== Changelog ==

= 1.5.3 =
* Split the mobile devices token in to chunks of 100 for sending messages in group.This will help when we have more number of users and we need to send pushntoification at once.Also set Maximum execution time.

= 1.5.2 =
* Description About The Custom Message Via Pushnotification.
* Resolve Notice Error.
* UI For Setting Page And Custom Pushnotification Better.

= 1.5.1 =
* Bug fixings.

= 1.5 =
* Implement WP REST API Functionality.
* Bug fixings.

= 1.4 =
* Bug fixings.

= 1.3 =
* Issue fixings.

= 1.2 =
* Bug Fix: In login, device token was not getting updated - Which is solved now.

= 1.1 =
* Added plugin support for Firebase Cloud Messaging(FCM) , On deactivation All the settings will be removed , On delete the plugin tables and all settings will drop.
* Fixed redirection issue of setting page.
* Updated custom and default message when User send notification via Custom message , add/edit post or page.

= 1.0 =
* First public release.

== Arbitrary section ==
Que) How to implement the device token and os type. Is it in the web header source code OR  in the app source code? . Need example API URL?S and parameters to register the device_token.

Ans -Please follow the below steps to achieve the pushnotificaiton in your mobile devices successfully

	- Activate Push Notifications for WP through the Plugins menu in WordPress.when you active the plugin the plugin will add 2 new tables in your wordpress database like 'all_pushnotification_logs' , 'all_pushnotification_token' . Please make sure that those tables are exist in your current activated Database.

	- Set the settings as per your requirement through wp-admin part of plugin.

	- please add FCM/GCM key if you want to pushnotification for android and Please upload the .pem certificate if you want pushnotifcaiton for ios.plese refer http://stackoverflow.com/questions/21250510/generate-pem-file-used-to-setup-apple-push-notification for .pem file.
 

	Now , 

	- There are two main aspects first - wordpress website which run on desktop / laptop  which have a wp-admin.  Second - the application which you created for mobile devices with ios/android.
	
	 - To send a push notification to a device, we must need to know its token (or Device ID) which has to be provided by the app through this API. And the type of operating sytem which mobile is useing .

	 - For Registering a device you need to follow steps.
		 
	 - First of all you have to insert the mobile device type and mobile device token of each devices in this table. For that you need to  pass the device type and devices token in parameters from your application services like login  , register service to the URL " http://yourwordpresssite/apnwp/register " . It means whenever user register or login from your application you need to pass that particular mobile's token and mobile operation system type android / ios.
	 
	 - 'wp-json' or 'WordPress REST API'  that shipped as part of WordPress core from version 4.4 Later. 

	URL structure:

	http://yourwordpresssite/wp-json/apnwp/register

	Method:  GET

	Parameters:
	device_token (string): token given by APNs or FCM identifying the device, often called device ID.
	os_type (string): operating system. It must be ios or android (case sensitive).
	user_email_id (string, optional): the user email address which is login from your mobile app.

	Examples:
	
	http://yourwordpresssite/wp-json/apnwp/register?os_type=android&user_email_id=androidmobile@40test.com&device_token=1234567890
	
	
	Refere Document:

	- more details how to pass the parameters in Android -  https://www.codementor.io/flame3/tutorials/send-push-notifications-to-android-with-firebase-du10860kb

	- more details how to pass the parameters in ios - https://www.raywenderlich.com/123862/push-notifications-tutorial
	
	- After passing the parameters when user successfully register or login please check the database table	`all_pushnotification_token` and check the token and OS type is inserted successfully or not.



== Upgrade Notice ==

= 1.5.3 =
* Split the mobile devices token in to chunks of 100 for sending messages in group.This will help when we have more number of users and we need to send pushntoification at once.Also set Maximum execution time.

= 1.5.2 =
* Description About The Custom Message Via Pushnotification
* Resolve Notice Error.
* UI For Setting Page And Custom Pushnotification Better.

= 1.5.1 =
* Bug fixings.

= 1.5 =
* Implement WP REST API Functionality.
* Bug fixings.

= 1.4 =
* Bug fixings.

= 1.3 =
* Issue fixings.

= 1.2 =
* Bug Fix: In login, device token was not getting updated - Which is solved now.

== Translations ==

* English - default, always included