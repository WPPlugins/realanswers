<?php
//The following is codes for redo form
//create form function found in realanswers_function.php
//$rsapi from realanswers_rest_api.php

//populate form according to question_id which is a secret string id!
$sid = $_GET['question_id'];
//check form submit status
//if not submitted, means new redo query, proceed to populate form using redo get api
if(!isset($_POST['process'])== 'yes_process'){
        
		//get data from apit
		global $rsapi;
		$redo_ques = $rsapi->redo($sid);
		
		//if empty response print service unavailable message
		if(empty($redo_ques)){
		$htm = "<div class='sidebar_error'>Service is Unavailable</div>";
		return $htm;
		}
		
		//check xml response is not empty
		if(!empty($redo_ques)){
		
		//check xml status is not 500
		if($redo_ques->status['code']=='500'){
		$error_message=$redo_ques->status->messages->message;
        return "<p>$error_message</p>";
		}
		
		//print_r($redo_ques);
        
		//extract response xml object and populate form values.
		$title = $redo_ques->question->title;
		$body = $redo_ques->question->body;
		$context = $redo_ques->question['context'];
		$location = $redo_ques->question->location;
		$fname = $redo_ques->question->fname;
		$lname = $redo_ques->question->lname;
		$email = $redo_ques->question->email;
		$tags = $redo_ques->question->tags->tag;
		if(!empty($tags)){
		foreach ($redo_ques->question->tags->tag as $tagged){
		$tags .= $tagged." ";
		 }
		}
		$tag = substr($tags, 0, -1); 
		
		
//create_realanswers_question_form($formtitle,$context,$location,$subject,$body,$tag,$notify_me,$fname,$lname,$email,$status_message)
$real_edit_form = create_realanswers_question_form('Edit Question',$context,$location,$title,$body,$tag,'',$fname,$lname,$email,'');
return $real_edit_form;

  }//end if(!empty($redo_ques))
}

//else if form is submitted, get all form values and post to realtybaron post api, check for status code;
elseif(isset($_POST['process'])== 'yes_process'){

//data posted from form assigned to variables to be posted to realtybaron post api
$context = $_POST['location_type'];
$location = $_POST['location_name'];
$title = $_POST['subject'];
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
$postdata = "location=$location&subject=$title&body=$body&$combine_tags&notify_me=$notify_me&open_days=$open_days&redo_url=$redo_url&answer_url=$answer_url&api_key=$api_key&fname=$fname&lname=$lname&email=$email&tou=$tou";

	 //use php curl extension to post data to realtybaron post api and parse xml response.

	 global $rsapi;
	 
	 $post_response = $rsapi->post($context,$postdata);
	 
	 $xml = new SimpleXMLElement($post_response);
	 //get status code 400 error 200 ok
	 $status_code = $xml->status['code'];
		
	 //print_r($xml);
		 
	 if($status_code == '200'){// status ok show message and set form status to blank
	
		   foreach($xml->links->link as $link){
		   
		   if($link['rel'] == "answers"){
		   
		   $status_message = "<div class='new_question_error'><p class='new_question_message'>";
		   $status_message .= "Your Question has been successfully sent. We will redirect you to the Question page. Please wait..";
		   $status_message .= "</p></div>";
			
		   $redirect_url = '<script language="javascript">';
		   $redirect_url .= 'window.location="';
		   $redirect_url .= $link['href'];
		   $redirect_url .= '";';
		   $redirect_url .= "</script>";
			   
		   return $status_message.$redirect_url;
		   
		   }
	   
	   }//end foreach
      
	}elseif($status_code == '400'){// status error show error message and set form status to redo
	 
	 $form_status = 'redo';
	 $status_message = "<div class='new_question_error'><ul>"; 
	 foreach ($xml->status->messages->message as $mess){ 
		 $status_message .= "<li>".$mess."</li>";
		 }
     $status_message .= "</ul></div>";
	 
	 }//end if($status_code == '200')

}//end if(isset($_POST['process'])== 'yes_process')

if($form_status == 'redo'){
//if status code 400 error found, repopulate form
$real_edit_form = create_realanswers_question_form('Edit Question',$context,$location,stripslashes($title),stripslashes($body),stripslashes($tag),'',$fname,$lname,$email,$status_message);
return $real_edit_form;
}
//end of redo.php!
?>