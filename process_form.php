<?php
//post data to realtybaron realanswers post api using PHP CURL extension.
//process response from realtybaron api to determine status of form submission
//whether it is success or bad request!

//include wp-config
$root = dirname(dirname(dirname(dirname(__FILE__))));
if (file_exists($root.'/wp-load.php')) {
// WP 2.6
require_once($root.'/wp-load.php');
} else {
// Before 2.6
require_once($root.'/wp-config.php');
}

/*******************The below checks post from form********************/

if(isset($_POST['process'])== 'yes_process'){
								
//check wp_nonce first!
//nonce pass from webpage
$nonce_value = $_POST['_wpnonce'];
//security check using nonce created by wp_create_nonce('realanswers-nonce');
//so as to determine data come from webpage
if (!wp_verify_nonce($nonce_value, 'realanswers-nonce') ) die('Failed Security check'); 

//data posted from form assigned to variables to be posted to realtybaron post api
$context = $_POST['location_type'];
$location = $_POST['location_name'];
$subject = $_POST['subject'];
$body = $_POST['question_detail'];
$tag = $_POST['tags'];

//The following split up tags and rejoin as tag=tag1&tag=tag2
$post_tags = null;//empty variable
$split_tags = explode(" ", $tag);//split up space and assign to array $split_tags
foreach ($split_tags as $split_tag){//combine string back as tag=tag1&tag=tag2&
$post_tags .= "tag=".$split_tag."&";
}
//remove the last & from the combined string and assign to $postdata!
$combine_tags = substr($post_tags, 0, -1); 
 
$notify_me = $_POST['notify_me'];
$open_days = 3;
$construct_redo_url = get_bloginfo('url')."/realanswers/redo?question_id={0}";
$redo_url = $construct_redo_url;
$construct_answer_url = get_bloginfo('url')."/realanswers/answers?question_id={0}";
$answer_url = $construct_answer_url;
$api_key = get_option('real_apikey');
$fname = $_POST['fname'];
$lname = $_POST['lname'];
$email = $_POST['email'];
$tou = $_POST['tou'];

//construct post data to be posted to realtybaron post api
$postdata = "location=$location&subject=$subject&body=$body&$combine_tags&notify_me=$notify_me&open_days=$open_days&redo_url=$redo_url&answer_url=$answer_url&api_key=$api_key&fname=$fname&lname=$lname&email=$email&tou=$tou";

	 //use php curl extension to post data to realtybaron post api and parse xml response.
	 
	 global $rsapi;
	 
	 $post_response = $rsapi->post($context,$postdata);
	 
	 /****Start error checking*****/
	 
	 //check if empty response from api redirect with service unavailable message
	 if(empty($post_response)){
	 $redo_url = get_bloginfo('url')."/realanswers/newquestions";
	 header("location:$redo_url/?status=redo&status_message=Service Unavailable");
	 }
	 
	 //if not empty response try whether it is xml, if not redirect back with service unavailable message
	 try{
	 $xml = @new SimpleXMLElement($post_response);
	   //check if there is status code, if not it is probably 404 or 503 apache html response
	   if(empty($xml->status['code'])){
	   $redo_url = get_bloginfo('url')."/realanswers/newquestions";
	   header("location:$redo_url/?status=redo&status_message=Service Unavailable");
	   }
     }	 
	 catch(Exception $e)
     {
	  //xml error message of unable to parse string as xml
	  //$message = $e->getMessage();
	  //construction url back to newquestion form redirect back with service unavailable message
	  $redo_url = get_bloginfo('url')."/realanswers/newquestions";
	  header("location:$redo_url/?status=redo&status_message=Service Unavailable");
	 }
	 
     /*********end error checking************/
	 
	 
	 //get status code 400 error 200 ok
	 $status_code = $xml->status['code'];
		 
	 if($status_code == '200'){// status ok show user the answer.php
	
		   foreach($xml->links->link as $link){
		   
		   if($link['rel'] == "answers"){
		   
		   $redirection_link = $link['href'];
		   
		   header("location:$redirection_link");
		   
		   }
	   
	   }//end foreach
	   
     
	 }elseif($status_code == '400'){// status error show error message and set form status to redo
	 
	 $status_message = "<div class='new_question_error'><ul>"; 
	 foreach ($xml->status->messages->message as $mess){ 
		 $status_message .= "<li>".$mess."</li>";
		 }
     $status_message .= "</ul></div>";
	 
	 //url encode data before sending back to form, so as not to cause newline error in header() function.
	 $context_e = urlencode($context);
	 $location_e = urlencode($location);
	 $subject_e = urlencode($subject);
	 $body_e = urlencode($body);
	 $tag_e = urlencode($tag);
	 $notify_me_e = urlencode($notify_me);
	 $fname_e = urlencode($fname);
	 $lname_e = urlencode($lname);
     $email_e = urlencode($email);
	 $message_e = urlencode($status_message);

	 //construction url back to newquestion form
	 $redo_url = get_bloginfo('url')."/realanswers/newquestions";
	 
	 //create form function as example for perimeters to send back.
	 //create_realanswers_question_form('Ask a Question',$context,$location,$subject,$body,$tag,$notify_me,$fname,$lname,$email,$status_message);
	 
	 //create content to send back to form in newquestions.php
	 $content = "status=redo&context=$context_e&location=$location_e&subject=$subject_e&body=$body_e&tag=$tag_e";
	 $content .= "&notify_me=$notify_me_e&fname=$fname_e&lname=$lname_e&email=$email_e&status_message=$message_e";

     header("location:$redo_url/?$content");
	 
	 }//end if($status_code == '200')

}//end if(isset($_POST['process'])== 'yes_process')


/**Since version 2.1 process API registration form!*********************/

if(isset($_POST['process_register_form'])== 'yes_process'){
	
	$nonce_value = $_POST['_wpnonce'];
	
	if (!wp_verify_nonce($nonce_value, 'realanswers_ajax_nonce') ) die('Failed Security check');
	$fname = $_POST['fname'];
	$lname = $_POST['lname'];
	$email = $_POST['email'];
	$role = $_POST['role'];
	$fname = urlencode($fname);
	$lname = urlencode($lname);
	$email = urlencode($email);
	
	$postdata = "fname=$fname&lname=$lname&email=$email";


	global $rsapi;
	 
	$post_response = $rsapi->register_api_key($role,$postdata);
		
	 /****Start error checking*****/
	 
	 //check if empty response from api response with service unavailable message
	 if(empty($post_response)){
     echo '<div id="register_error_message" class="updated fade"><p><strong>Sorry, Service Unavailable.';
	 echo ' Please try again later</p></strong></div>';
	 die(); 
	 }
	 
	 //if not empty response try whether it is xml, if not response with service unavailable message
	 try{
	 $xml = @new SimpleXMLElement($post_response);
	   //check if there is status code, if not it is probably 404 or 503 apache html response
	   if(empty($xml->status['code'])){
       echo '<div id="register_error_message" class="updated fade"><p><strong>Sorry, Service Unavailable.';
	   echo ' Please try again later</p></strong></div>';
	   die();  
	   }
     }	 
	 catch(Exception $e)
     {
	  //xml error message of unable to parse string as xml
	  //$message = $e->getMessage();
	  //response with service unavailable message
     echo '<div id="register_error_message" class="updated fade"><p><strong>Sorry, Service Unavailable.';
	 echo ' Please try again later</p></strong></div>';
	 die(); 
	 }
	 
     /*********end error checking************/
	 
	 	 //get status code 400 error 200 ok
	 $status_code = $xml->status['code'];
	 
	 if($status_code == '500'){//server internal error
	
	//echo error message
	echo "<div id=\"message\" class=\"updated fade\">";
	echo "<strong><p>Sorry, registration was not successful. Please try again later.</p></strong>";
	echo "</div>";
	   
	  }//end foreach
		 
	 
	 if($status_code == '400'){// error
	
	 //parse error message response and show to user
	 $status_message = "<div id=\"register_error_message\" class=\"updated fade\"><p><strong>The following needs your attention!</strong></p><ol>"; 
	 foreach ($xml->status->messages->message as $mess){ 
		 $status_message .= "<li>".$mess."</li>";
		 }
     $status_message .= "</ol></div>";
	 
	 echo $status_message;
	   
	   }//end foreach
	   
	 
	 if($status_code == '200'){// success
	 
         $response_api_key = $xml->id;
		 
		 //cast xml object into array
		 $response_api_key_array = (array)$response_api_key;
		 //assign index [0] which is the api key to updated into option
		 $update_in_option_apikey = $response_api_key_array[0];
	   
	     update_option('real_apikey',$update_in_option_apikey);
	
	//echo script to hide admin warning
	echo"<script type='text/javascript'>$('#realanswers-warning').hide();$('#register_form_back_link').hide();</script>";
	
	$admin_setting_url = admin_url()."options-general.php?page=realanswers_admin.php";
		 
	//echo success message in header!
	echo "<div id=\"message\" class=\"updated fade\">";
	echo "<strong><p>Your registration was successful! <a style=\"text-decoration: none;\" href=\"$admin_setting_url\">Please click here to setup remaining options.</a></p></strong>";
	echo "</div>";
	   
	   }//end foreach
	 
}//end if(isset($_POST['process_register_form'])== 'yes_process')











































?>