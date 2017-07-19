<?php
//functions other than the realanswers rest api are found below.
// load scripts into theme
function load_realanswers_script() {
  //since version 2.1
  //register jquery cdn
  wp_register_script('realanswers_jquery_cdn', 'http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js');
//since version 2.1
//register autocompleter
  wp_register_script('realanswers_autocompleter', WP_PLUGIN_URL . '/realanswers/jquery.autocomplete.pack.js', array('realanswers_jquery_cdn'), $ver);
//register realanswers_form_validation.js script
  wp_register_script('realanswers_form_validation', plugins_url('realanswers_form_validation.js', __FILE__));
//since version 2.1
//load auto completer
  if (!is_admin()) {
    wp_enqueue_script('realanswers_autocompleter', WP_PLUGIN_URL . '/realanswers/jquery.autocomplete.pack.js', array('realanswers_jquery_cdn'), $ver);
//load realanswers_form_validation.js script
    wp_enqueue_script('realanswers_form_validation', plugins_url('realanswers_form_validation.js', __FILE__));
// load api style
    wp_enqueue_style('realanswers_default_css', 'http://api.realtybaron.com/answers/css/default.css', $deps, '1.0', 'screen');
  }
}

//add script to theme <head>
add_action('init', 'load_realanswers_script');
//function to delete options after deleting plugin
function realanswers_remove_options_hook() {
  //delete option
  delete_option('real_apikey', $real_apikey);
  delete_option('real_max_results', $real_max_results);
  delete_option('real_location_type', $real_location_type);
  delete_option('real_location_value', $real_location_value);
  delete_option('real_custom_css', $real_custom_css);
}

//registering uninstall hook
if (function_exists('register_uninstall_hook')) {
  register_uninstall_hook(__FILE__, 'realanswers_remove_options_hook');
}
//function to hook location value as html <title> in <head>
/*************hook in value as html <title> in <head> For SEO purpose************************/
/***For questions***/
//declare global variable
$realanswers_dynamic_question_title;
function get_question_title($title) {
  global $realanswers_dynamic_question_title;
  $realanswers_dynamic_question_title = $title;
}

function hook_question_html_title($content) {
  if (is_page('realanswers/questions')) {
    global $realanswers_dynamic_question_title;
    $doc_title = $realanswers_dynamic_question_title;
    $html_title = "Recent Questions in " . $doc_title . " - ";
    echo $html_title;
  } else {
    echo $content;
  }
}

//hook in html <title> in <head>
add_filter('wp_title', 'hook_question_html_title');
/***end For questions***/
/***For Answers***/
//declare global variable
$realanswers_dynamic_title;
//use function to grab title of answers page
//from api and assign to global variable $realanswers_dynamic_title;
//This function is used in answers.php
function grab_title_for_wp_title($title) {
  global $realanswers_dynamic_title;
  $realanswers_dynamic_title = $title;
}

//function to hook answers titles
function hook_answers_html_title($content) {
  if (is_page('realanswers/answers')) {
    //grab title from global variable
    global $realanswers_dynamic_title;
    $html_title = $realanswers_dynamic_title;
    return $html_title . " - ";
  } else {
    echo $content;
  }
}

//hook in html <title> in <head>
add_filter('wp_title', 'hook_answers_html_title');
/***end For Answers***/
/***For newquestions***/
//declare global variable
$realanswers_dynamic_form_title;
//use function to grab title of newquestions page
//This function is used in newquestions.php
function grab_question_title_for_wp_title($title) {
  global $realanswers_dynamic_form_title;
  $realanswers_dynamic_form_title = $title;
}

//function to hook newquestions titles
function hook_newquestions_html_title($content) {
  if (is_page('realanswers/newquestions')) {
    //grab title from global variable
    global $realanswers_dynamic_form_title;
    $html_title = $realanswers_dynamic_form_title;
    return $html_title . " - ";
  } else {
    echo $content;
  }
}

//hook in html <title> in <head>
add_filter('wp_title', 'hook_newquestions_html_title');
/***end For Answers***/
/*****end of hook titles for SEO purpose******/
/****function to create new question form****/
function create_realanswers_question_form($formtitle, $context, $location, $subject, $body, $tag, $notify_me, $fname, $lname, $email, $status_message) {
  //remove slashes added in message by PHP, so that style classes are not escaped!
  $status_messages = stripslashes($status_message);
//create form
  $form = '<div class="realanswers_question_form">';
  $form .= "<h2 class='question_form_title'>$formtitle</h2>";
  $post_url_raw = WP_PLUGIN_URL . "/realanswers/process_form.php";
  $post_url = esc_url($post_url_raw);
  $form .= "<form action=\"$post_url\" method=\"post\" name=\"question_form\" id=\"question_form\">";
//form errors
  $form .= "<div id=\"realanswers_form_errors\">$status_messages</div>";
//subject
  $form .= '<div id="form_subject" class="form_elements">';
  $form .= '<label id="subject_label">Subject:</label>';
  $form .= "<input name=\"subject\" id=\"subject\" type=\"text\" value=\"$subject\" style=\"width:100%\"  maxlength=\"90\">";
  $form .= '</div>';
//body
  $form .= '<div id="form_body" class="form_elements">';
  $form .= '<label id="question_detail_label">Question Detail:</label>';
  $form .= "<textarea name=\"question_detail\" id=\"question_detail\" style=\"width:100%;height:100px\">$body</textarea>";
  $form .= '</div>';
//tags
  $form .= '<div id="form_tags" class="form_elements">';
  $form .= "<label id='tags_label'>Tags: </label><input name=\"tags\" id=\"tags\" type=\"text\" value=\"$tag\" maxlength=\"90\">";
  $form .= '</div>';
//location type
//onchange event real_autocomplete will trigger autocompleter!
  $form .= '<div id="form_location" class="form_elements form_location_elements">';
  $form .= '<label id="location_label">Location: </label>';
  $form .= '<select id="location_type" name="location_type"  col="50px" onchange="real_autocomplete(\'location_type\',\'#location_name\');">';
//determine location type
  $real_loca = $context;
  if ($real_loca == 'zip') {
    $real_loca = 'zipcode';
  }
  if ($real_loca == 'metro') {
    $metro = 'selected="selected"';
  }
  ;
  if ($real_loca == 'city') {
    $city = 'selected="selected"';
  }
  ;
  if ($real_loca == 'zipcode') {
    $zipcode = 'selected="selected"';
  }
  ;
  if ($real_loca == 'address') {
    $address = 'selected="selected"';
  }
  ;
  $form .= "<option value=\"metro\" $metro>Metro</option><option value=\"city\" $city>City</option><option value=\"zipcode\" $zipcode>ZIP code</option><option value=\"address\" $address>Address</option></select> ";
//assign location value
  $real_loca_value = $location;
//location value
  $form .= " <input type='text' id='location_name' name='location_name' value='$real_loca_value' onmouseover='real_empty_this()'>";
  $form .= '</div>';
//check status of notify me check box
  if ($notify_me == 'true' || !empty($fname) || !empty($lname) || !empty($email)) {
    $notify_check = checked;
    $particular_display = 'block';
  } else {
    $particular_display = 'none';
  }
//First name
  $form .= "<div id=\"realanswers_particulars\" style=\"display:$particular_display;margin:0px 0px 20px 0px\">";
  $form .= '<div id="form_fname" class="form_elements">';
  $form .= "<label id='fname_label'>First Name: </label><input name=\"fname\" id=\"fname\" value=\"$fname\" type=\"text\" maxlength=\"90\">";
  $form .= '</div>';
//Last name
  $form .= '<div id="form_lname" class="form_elements">';
  $form .= "<label id='lname_label'>Last Name: </label><input name=\"lname\" id=\"lname\" value=\"$lname\" type=\"text\" maxlength=\"90\">";
  $form .= '</div>';
//Email
  $form .= '<div id="form_email" class="form_elements">';
  $form .= "<label id='email_label'>Email Address: </label><input name=\"email\" id=\"email\" value=\"$email\" type=\"text\" maxlength=\"90\">";
  $form .= '</div>';
  $form .= '</div>'; //end of div real answers particulars
//Terms of use
  $form .= '<div id="form_tou" class="form_elements">';
  $form .= '<label id="tou_label">Terms of Use: </label><p class="tou_terms">I understand the advice or opinions I received will NOT constitute a legal binding client relationship with any of the real estate professionals who respond to my question.</p>';
  $form .= '</div>';
//terms of use in hidden input
  $form .= '<div id="form_tou_hidden" class="form_elements">';
  $form .= "<input name=\"tou\" id=\"tou\" value=\"I understand the advice or opinions I received will NOT constitute a legal binding client relationship with any of the real estate professionals who respond to my question.\" type=\"hidden\" >";
  $form .= '</div>';
//notify checkbox
  $form .= '<div id="form_checkbox" class="form_elements">';
  $form .= "<input name=\"notify_me\" id=\"notify_me\" type=\"checkbox\" value=\"true\" onclick=\"show_realanswers_particulars()\" $notify_check ><label id='notify_me_label'>Email me when answers to this question are posted</label>";
  $form .= '</div>';
//create nonce for checking in process_form before posting to api.
//so as to determine data posted from form and not elsewhere.
  $nonce = wp_create_nonce('realanswers-nonce');
//submit button
  $form .= '<div id="form_submit" class="form_elements">';
  $form .= "<input type=\"hidden\" name=\"_wpnonce\" value=\"$nonce\"/>";
  $form .= '<input type="hidden" name="process" value="yes_process"/>';
  $form .= '<input type="submit" id="form_submit_button" value="Post Question">';
  $form .= '</div>';
  $form .= '</form>';
  $form .= '</div>';
  return $form;
}

//Since Version 2.1
/****function to create registration form****/
function create_realanswers_register_form($fname, $lname, $email, $email2, $role, $status_message) {
  $check_real_apikey = get_option('real_apikey');
  if (empty($check_real_apikey)) {
    $display = 'display:block;';
  } else {
    $display = 'display:none;';
  }
//remove slashes added in message by PHP, so that style classes are not escaped!
  $status_messages = stripslashes($status_message);
//create form
  $form = "<div class=\"realanswers_register_form\" id=\"realanswers_register_form\"style=\"$display\">";
//title
  $form .= "<h3 class='realanswers_register_form_title'>New Account Registration <span style='font-size:12px; margin:0 0 0 0;'><a href='#' id='register_form_back_link' style='text-decoration:none;' onclick='real_hide_register_form()'>(If you already have an API key, click here to enter it now.)</a></span></h3>";
  $post_url_raw = WP_PLUGIN_URL . "/realanswers/process_form.php";
  $post_url = esc_url($post_url_raw);
  $form .= "<form name=\"register_form\" id=\"register_form\" onsubmit='real_ajax_register_form();return false;'>";
//form errors
  $form .= "<div id=\"realanswers_register_form_errors\">$status_messages</div>";
  $form .= "<table class='widefat'>";
  $form .= "<tr>";
//fname
  $form .= "<td width='200px'><label id='fname_label'>Your First Name: </label></td><td><input name=\"fname\" id=\"fname\" value=\"$fname\" type=\"text\" maxlength=\"90\" size=\"50\"></td>";
  $form .= '</tr>';
//lname
  $form .= '<tr class="odd">';
  $form .= "<td><label id='lname_label'>Your Last Name: </label></td><td><input name=\"lname\" id=\"lname\" value=\"$lname\" type=\"text\" maxlength=\"90\" size=\"50\"></td>";
  $form .= '</tr>';
//email
  $form .= '<tr>';
  $form .= "<td><label id='email_label'>Your Email Address: </label></td><td><input name=\"email\" id=\"email\" value=\"$email\" type=\"text\" maxlength=\"90\" size=\"50\"></td>";
  $form .= '</tr>';
//email2 retype
  $form .= '<tr class="odd">';
  $form .= "<td><label id='email2_label'>Your Email Address (retype): </label></td><td><input name=\"email2\" id=\"email2\" value=\"$email2\" type=\"text\" maxlength=\"90\" size=\"50\"></td>";
  $form .= '</tr>';
//role - account type
//check button status
  $form .= '<tr>';
  $form .= "<td><label id='role_label'>Your Role: </label></td>";
  $form .= "<td><input type=\"radio\" name=\"role\" id=\"role\" value=\"AGENT\"";
  if (empty($role) || ($role == 'AGENT')) {
    $check = 'checked';
  }
  $form .= $check . "><label style='margin:0 0 0 10px'>Real Estate Professional</label>";
  $form .= "<br /><input type=\"radio\" name=\"role\" id=\"role\" value=\"PARTNER\"";
  if ($role == 'PARTNER') {
    $check2 = 'checked';
  }
  $form .= $check2 . "><label style='margin:0 0 0 10px'>Other (i.e. webmaster, publisher, SEO marketer, etc.)</label>";
  $form .= '</td></tr>';
//create nonce for checking in process_form before posting to api.
//so as to determine data posted from form and not elsewhere.
  $nonce = wp_create_nonce('realanswers-nonce');
  $form .= "</table>";
//submit button
  $form .= '<p>';
  $form .= '<input type="submit" id="form_submit_button" value="Submit" class="button-primary">';
  $register_ajax_image_src = WP_PLUGIN_URL . "/realanswers/indicator.gif";
  $form .= "<img style=\"position: relative; top: 5px; display: none;\" id=\"register_ajax_indicator\" alt=\"register_indicator\" src=\"$register_ajax_image_src\">";
  $form .= '</p>';
  $form .= '</form>';
  $form .= '</div>';
  return $form;
}

//Since Version 2.1
//function add script to head of blog for sutocompleter in new questions form
//because of plugin url maybe different for blogs, we had to make it dynamic
//rest of javascript functions in realanswsers_form_validation.js
function hook_autocomplete_javascript() {
  $plugin_url = WP_PLUGIN_URL;
  echo <<<END
<script type="text/javascript">
$(document).ready(function() {
var location = document.getElementById('location_type').value;
$('#location_name').focus().autocomplete("$plugin_url/realanswers/location-value-json.php",{
	width:240,
	highlight:false,
	extraParams:{type:location}
	});
});

//function for dynamic autocompleter!
//trigger by onchange event in new question form location type
function real_autocomplete(type,value){
    var location_type = document.getElementById('location_type');
    var location_name = document.getElementById('location_name');
    if (location_type.selectedIndex == 0) {
        location_name.value = 'What Metro?';
 
    }
    if (location_type.selectedIndex == 1) {
        location_name.value = 'What City?';

    }
    if (location_type.selectedIndex == 2) {
        location_name.value = 'What Zipcode?';

    }
    if (location_type.selectedIndex == 3) {
        location_name.value = 'What Address?';
  
    }

   //clear autocomplete
   $(value).unautocomplete();
   //assign location type to variable location
   //for sending over to location-value-json.php
   var location = document.getElementById(type).value;
   //start auto completer class
   $(value).focus().autocomplete("$plugin_url/realanswers/location-value-json.php",{
	width:240,
	highlight:false,
	extraParams:{type:location}
	});
}
</script>
<style type="text/css">
/**autocompleter css**/
.ac_results {
	padding: 0px;
	border: 1px solid black;
	background-color: white;
	overflow: hidden;
	z-index: 100;
}

.ac_results ul {
	width: 100%;
	list-style-position: outside;
	list-style: none;
	padding: 0;
	margin: 0;
}

.ac_results li {
	margin: 0px;
	padding: 2px 5px;
	cursor: pointer;
	display: block;
	/* 
	if width will be 100% horizontal scrollbar will apear 
	when scroll mode will be used
	*/
	/*width: 100%;*/
	font: menu;
	font-size: 12px;
	/* 
	it is very important, if line-height not setted or setted 
	in relative units scroll will be broken in firefox
	*/
	line-height: 16px;
	overflow: hidden;
	text-align:left;
}

.ac_loading {
	background: white url("$plugin_url/realanswers/indicator.gif") right center no-repeat;
}

.ac_odd{
	background-color:#eeeeee;
}
.ac_even{
	background-color:#ffffff;
}
/*backward compatibility*/
.ac_over{
    background-color:#44c7f4;
	color:#fff;
}
.ac_over:hover{
    background-color:#44c7f4;
	color:#fff;
}
/*controls new question form api validation css*/
/*highlights yellow and aligns errors to left*/
.new_question_error ul li{
text-align:left;
}
.label-highlight{
text-align:left;
background-color:#00FF00;
}
</style>
END;
}

//hook in html <head>
add_action('wp_head', 'hook_autocomplete_javascript');
?>