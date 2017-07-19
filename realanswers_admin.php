<?php
//register all settings
function register_realanswers_options_parameter() {
    register_setting('realanswers_settings', 'real_apikey');
    register_setting('realanswers_settings', 'real_max_results');
    register_setting('realanswers_settings', 'real_custom_css');
    register_setting('realanswers_settings', 'real_captcha_public');
    register_setting('realanswers_settings', 'real_captcha_private');
    register_setting('realanswers_settings', 'real_location_count');
}

add_action('admin_init', 'register_realanswers_options_parameter');

//constructs admin page
function realanswers_admin() {

    if (isset($_POST['submitted1']) == 'update_keys') {

        //check nonce
        check_admin_referer('realanswers_settings-options');

        $rs_error = array(); //declare errors as array

        //check for errors in posted values!

        if (empty ($_POST['real_apikey'])) {
            $rs_error[] = 'API KEY is empty, please key in API KEY from RealtyBaron.';
        }

        //check reCAPTCHA public key
        if (empty ($_POST['real_captcha_public'])) {
            $rs_error[] .= 'reCAPTCHA Public Key: is empty, please key a Public API KEY from reCAPTCHA.';
        }

        //check reCAPTCHA private key
        if (empty ($_POST['real_captcha_private'])) {
            $rs_error[] .= 'reCAPTCHA Private Key: is empty, please key a Private API KEY from reCAPTCHA.';
        }

        if (empty ($_POST['real_max_results']) || !ctype_digit($_POST['real_max_results'])) {
            $rs_error[] = 'Max results in sidebar: is empty or non-digit entered. Please enter a number, such as 5.';
        }

        //assign posted value
        $real_apikey = $_POST['real_apikey'];
        $real_max_results = stripslashes($_POST['real_max_results']);
        $real_captcha_public = stripslashes($_POST['real_captcha_public']);
        $real_captcha_private = stripslashes($_POST['real_captcha_private']);


        // if no error proceed to update value into options database
        if (empty($rs_error)) {
            //update post value into WordPress Database
            update_option('real_apikey', $real_apikey);
            update_option('real_max_results', $real_max_results);
            update_option('real_captcha_public', $real_captcha_public);
            update_option('real_captcha_private', $real_captcha_private);

            //print success message
            echo "<div  id=\"message\" class=\"updated fade\"><p><strong>API Keys Updated!</strong></p></div>";

        }
            //else if error found
        else {

            //Display error message!
            echo "<div id=\"message\" class=\"updated fade\"><p><strong>There are problems with the following field(s),</strong></p><ol>";

            foreach ($rs_error as $rs_errors) {
                echo "<li>$rs_errors</li>\n";
            }

            echo "</ol><p><strong>Please note that, empty fields will revert to last settings updated by you.</strong></p></div>";

        }
        //end of checking error

    }
    //end of if(isset($_POST['submitted1'])== 'update_keys')


    if (isset($_POST['submitted2']) == 'update_locations') {

        //check nonce
        check_admin_referer('realanswers_settings-options');

        $rs_error = array(); //declare errors as array


        //update location option
        $check_location_count = get_option('real_location_count');
        if (empty($check_location_count)) {
            $check_location_count = 0;
        }
        for ($count = 0; $count <= $check_location_count; $count++) {
            if (empty ($_POST["real_location_value$count"])) {
                $location_error_count = $count + 1;
                $rs_error[] .= "Location Value $location_error_count is empty,
					 please key in location Value or delete option.";
            } else {
                $real_location_value[$count] = stripslashes($_POST["real_location_value$count"]);
                update_option("real_location_value", $real_location_value);
                $real_location_type[$count] = stripslashes($_POST["real_location_type$count"]);
                update_option("real_location_type", $real_location_type);
            }

        }

        // if no error proceed to update value into options database
        if (empty($rs_error)) {

            //print success message
            echo "<div  id=\"message\" class=\"updated fade\"><p><strong>Location Values Updated!</strong></p></div>";

        }
            //else if error found
        else {

            //Display error message!
            echo "<div id=\"message\" class=\"updated fade\"><p><strong>There are problems with the following field(s),</strong></p><ol>";

            foreach ($rs_error as $rs_errors) {
                echo "<li>$rs_errors</li>\n";
            }

            echo "</ol><p><strong>Please note that, empty fields will revert to last settings updated by you.</strong></p></div>";

        }
        //end of checking error

    }
    //end of if(isset($_POST['submitted2'])== 'update_locations')

    if (isset($_POST['submitted3']) == 'update_custom_css') {

        //check nonce
        check_admin_referer('realanswers_settings-options');


        //assign posted value
        $real_custom_css = stripslashes($_POST['real_custom_css']);

        // if no error proceed to update value into options database

        //update post value into WordPress Database
        update_option('real_custom_css', $real_custom_css);

        //print success message
        echo "<div  id=\"message\" class=\"updated fade\"><p><strong>Custom CSS Updated!</strong></p></div>";


    }//end of if(isset($_POST['submitted3'])== 'update_custom_css')

    ?>

    <div class="wrap">
        
    <?php
    //realtybaron logo
    $logo_url = WP_PLUGIN_URL . '/realanswers/realtybaron_logo.gif';
    echo "<div style='margin:10px 0px 0px 0px'><a href='http://realtybaron.com' target='_blank'><img src='$logo_url' alt='logo' id='logo' style='float:left;margin:0px 10px 0px -10px'></a>";
    ?><h2 style="padding:17px 0px 0px 0px;display:block">RealAnswers Settings</h2></div>
    
    <?php
	//Since Version 2.1
    //check api key to determine whether to show settings form or register form
	$check_real_apikey = get_option('real_apikey');

		if(empty($check_real_apikey)){
		$display_setting = 'display:none;';
		}else{
		$display_setting = 'display:block;';
		}
	
    ?>
    
    <?php 
	//Since Version 2.1
	//print out registration form
	echo create_realanswers_register_form($fname,$lname,$email,$email2,$role,$status_message);
	?>
    
    <div id="settings_form" style="<?php echo $display_setting?>">

    <div id='error_dialog' style="display:none"></div>

    <form name="RealanswersForm1" id="RealanswersForm1" method="post" action=""
          onsubmit="real_update_apikeys();return false;">

    <?php settings_fields('realanswers_settings'); ?>

        <p>
            <strong>API Key:</strong>
            <input type="text" id="real_apikey" name="real_apikey" style="width:200px"
                   value="<?php $real_apikey = get_option('real_apikey'); echo $real_apikey; ?>"
                   onchange="real_highlight_input();"/>
            (Need an API key? <a href="#" onclick="real_show_register_form()">Request
            one here</a>)
        </p>

        <p>
            <strong>Max results in sidebar:</strong>
            <input type="text" id="real_max_results" name="real_max_results" style="width:25px"
                   value="<?php $real_max_results = get_option('real_max_results'); echo $real_max_results; ?>"
                   onchange="real_highlight_input();"/>
        </p>

        <p class="submit">
            <input type="hidden" name="submitted1" value="update_keys"/>
            <input type="submit" name="submit" value="Update API Keys" class="button-primary"/>
        <?php
        //ajax image indicator
        $keys_indicator_url = WP_PLUGIN_URL . '/realanswers/indicator.gif';
        echo "<img src='$keys_indicator_url' alt='keys_indicator' id='keys_ajax_indicator' style='position:relative;top:5px;display:none;'/>";
        ?>
        </p>

    </form>

    <form name="RealanswersForm2" id="RealanswersForm2" method="post" action=""
          onsubmit="real_update_location_values();return false;">

    <?php settings_fields('realanswers_settings'); ?>

        <ul id="real_metro_ajax_response"><!--ajax response will add to list!-->

        <?php
        $check_location_count = get_option('real_location_count');

        if (empty($check_location_count)) {
            $check_location_count = 0;
        }

        for ($count = 0; $count <= $check_location_count; $count++) {

            //get location value from option table
            $real_location_val = get_option("real_location_value");
            $real_location_value[$count] = $real_location_val[$count];

            //get location type from option table
            $real_location_typ = get_option("real_location_type");
            $real_location_type[$count] = $real_location_typ[$count];

            //sortable handle image url
            $handle_url = WP_PLUGIN_URL . '/realanswers/arrow.png';

            echo"<li class='real_location_list' id='$count'><strong>Location:</strong>";

            echo"<select id='real_location_type$count' name='real_location_type$count' onChange='real_autocomplete(\"real_location_type$count\",\"#real_location_value$count\")'>";
            echo'<option value="metro"';
            if ($real_location_type[$count] == 'metro') {
                echo 'selected="selected"';
            }
            echo'>Metro</option>';
            echo'<option value="city"';
            if ($real_location_type[$count] == 'city') {
                echo 'selected="selected"';
            }
            echo'>City</option>';
            echo'<option value="zipcode"';
            if ($real_location_type[$count] == 'zipcode') {
                echo 'selected="selected"';
            }
            echo'>ZIP code</option>';
            echo'<option value="address"';
            if ($real_location_type[$count] == 'address') {
                echo 'selected="selected"';
            }
            echo'>Address</option>';
            echo'</select>';

            echo"<input type='text' id='real_location_value$count' name='real_location_value$count' style='width:202px' value='$real_location_value[$count]' onchange='real_highlight_input();'/>";
            echo"<img src='$handle_url' alt='move' width='16' height='16' title='Click to drag and sort!' class='handle' />";
            echo'</li>';

        }
        ?>
        </ul>

        <div id="add_location_count"
             style="position:relative;top:-29px;left:400px;text-decoration:underline;cursor:pointer;">[Add Another?]
        </div>
        <div id="remove_location_count"
             style="position:relative;top:-48px;left:510px;text-decoration:underline;cursor:pointer;"
             onclick="real_remove_location_option();">[Remove Location]
        </div>
    <?php
    //ajax image indicator
    $indicator_url = WP_PLUGIN_URL . '/realanswers/indicator.gif';
    echo "<img src='$indicator_url' alt='indicator' id='add_ajax_indicator' style='position:relative;top:-65px;left:640px;display:none;'/>";
    ?>

    <?php
    //echo out the jquery autocompleter setup
    //function will dynamically setup autocompleter according to number of location values added!
    //see function below for more details
    default_autocompleter();
    ?>

        <p class="submit">
            <input type="hidden" name="submitted2" value="update_locations"/>
            <input type="submit" name="submit" value="Update Locations" class="button-primary"/>
        <?php
        //ajax image indicator
        $location_indicator_url = WP_PLUGIN_URL . '/realanswers/indicator.gif';
        echo "<img src='$location_indicator_url' alt='location_indicator' id='location_ajax_indicator'  style='position:relative;top:5px;display:none;'/>";
        ?>
        </p>

    </form>

    <form name="RealanswersForm3" id="RealanswersForm3" method="post" action=""
          onsubmit="real_update_custom_css();return false;">

    <?php settings_fields('realanswers_settings'); ?>

        <strong>Custom CSS:</strong><br/>
        <textarea id="real_custom_css" name="real_custom_css" style="width:90%;height:300px;">
        <?php $real_custom_css = get_option('real_custom_css'); echo $real_custom_css;?>
        </textarea><br/>

        <p><u>Custom CSS Instructions</u></p>
        <ol>
            <li>Paste or type your Custom Style codes in the above textarea to block out Default Styles.</li>
            <li>You can view the Default CSS <a href="http://api.realtybaron.com/answers/css/default.css"
                                                target="_blank">here</a>.
            </li>
            <li>Please do not type in &lt;style type="text/css"&gt;&lt;/style&gt;</li>
            <li>Leave option blank if not using.</li>
        </ol>

        </p>

        <p class="submit">
            <input type="hidden" name="submitted3" value="update_custom_css"/>
            <input type="submit" name="submit" value="Update Custom CSS" class="button-primary"/>
        <?php
        //ajax image indicator
        $custom_indicator_url = WP_PLUGIN_URL . '/realanswers/indicator.gif';
        echo "<img src='$custom_indicator_url' alt='custom_indicator' id='custom_ajax_indicator' style='position:relative;top:5px;display:none;'/>";
        ?>
        </p>

    </form>

    </div><!--end of div class='settings fcrm'-->
    
    </div><!--end of div wrap-->

    <?php

}

//end of admin page

//add admin page to WordPress
function  realanswers_admin_addpage() {

    $realanswers_insert_java = add_options_page('RealAnswers', 'RealAnswers', 8, 'realanswers_admin.php', 'realanswers_admin');
    //print javascript in sb-insertcodes sub menu page only
    add_action("admin_print_scripts-$realanswers_insert_java", "hook_realanswers_autocompleter");

}

add_action('admin_menu', 'realanswers_admin_addpage');

//add javascript or css to admin settings page via action hook realanswers_hook_admin_script()
function hook_realanswers_autocompleter() {

    //assign values to variable for use in creating javascripts below!
    $site_url = get_bloginfo('url');
    $plugin_url = WP_PLUGIN_URL;
    $real_ajax_url = WP_PLUGIN_URL . '/realanswers/process_admin_ajax.php';
	$real_ajax_register_url = WP_PLUGIN_URL . '/realanswers/process_form.php';
    $real_nonce = wp_create_nonce('realanswers_ajax_nonce');

    echo <<<END

<!--The following scripts are generated by realanswers admin settings--->
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.1/jquery-ui.min.js"></script>
<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/ui-lightness/jquery-ui.css" rel="stylesheet" type="text/css"/>
<script type='text/javascript' src='$plugin_url/realanswers/jquery.autocomplete.pack.js'></script>

<!--scripts for autocompleter-->
<script type="text/javascript">

$().ready(function() {
//function to add location count and location option
	$('#add_location_count').click(function(){
	$('#add_ajax_indicator').show();
		   $.ajax({
				   url: '$real_ajax_url',
				   type: 'Post',
				   cache: false,
				   data:'add_location=yes&location_count=1&_wpnonce=$real_nonce',
				   success: function(data) {
				          $('#real_metro_ajax_response').append(data);
						  $('#add_ajax_indicator').hide();
				          }
			 });
		 });

});

//function to remove location option
function real_remove_location_option(){
if($('.real_location_list').is('li')&&!$('.real_location_list').is('li:only-child')){
	 $('#add_ajax_indicator').show();  
	   $.ajax({
               url: '$real_ajax_url',
			   type: 'Post',
			   cache: false,
			   data:'delete_location=yes&del_loca_count=1&_wpnonce=$real_nonce',
			   success:function(){
			           $('.real_location_list:last').remove();
					   $('#add_ajax_indicator').hide();
			           }
             });
	 }

}
//function for dynamic autocompleter!
//trigger by onchange event in location type select list.
function real_autocomplete(type,value){
   //empty location value
   $(value).val("");
   //clear autocomplete
   $(value).unautocomplete();
   //assign location type to variable location
   //for sending over to location-value-json.php
   var location = document.getElementById(type).value;
   //start auto completer class
   $(value).autocomplete("$plugin_url/realanswers/location-value-json.php",{
	width:200,
	highlight:false,
	extraParams:{type:location}
	});
}

//function for add new location autocompleter!
//called by process_admin_ajax.php
function real_add_autocomplete(value){
   //empty location value
   $(value).val("");
   //clear autocomplete
   $(value).unautocomplete();

   var location = "metro";
   //start auto completer class
   $(value).autocomplete("$plugin_url/realanswers/location-value-json.php",{
	width:200,
	highlight:false,
	extraParams:{type:location}
	});
}

//function for default location autocompleter!
//called by default_autocompleter() see below!
function real_default_autocomplete(type,value){
   //clear autocomplete
   $(value).unautocomplete();
   //assign location type to variable location
   //for sending over to location-value-json.php
   var location = type;
   //start auto completer class
   $(value).autocomplete("$plugin_url/realanswers/location-value-json.php",{
	width:200,
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
.widefat{background:none;}
.widefat tr td {border:none;height:30px;}
.widefat input {background:none;border:1px solid #666666}
.widefat tr {background-color: #eee;}
.widefat tr.odd {background-color: #fff;}
.ui-dialog .ui-dialog-buttonpane button {
cursor:pointer;
float:right;
margin:0.5em 0.4em 0.5em 0;
}
</style>

<!--scripts for sortable-->
<script type="text/javascript">
  // When the document is ready set up our sortable with it's inherant function(s)
$(document).ready(function() {
	 
    $("#real_metro_ajax_response").sortable({
      handle : '.handle',
	  placeholder: 'empty',

	});

});
</script>

<style type="text/css">
/**sortable css**/
#real_metro_ajax_response {
list-style:none;
}
#real_metro_ajax_response li {
	display: block;
	padding: 10px 10px;
	margin-bottom: 3px;
	background-color:#efefef;
	width:370px;
}
#real_metro_ajax_response li img.handle {
	margin:0px 0px 0px 5px;
	vertical-align:text-top;
	cursor:move;
}
.empty{height:25px;}
</style>

<!--scripts for AJAX update options!-->
<script type="text/javascript">
 
function real_update_apikeys(){

//show ajax indicator!
$('#keys_ajax_indicator').show();

//do form validation first!

var error_message = "";//declare global error message string!

//check API key
var check_api_key = $('#real_apikey').val();
if(check_api_key==0){
error_message+='<li>API Key is empty! Please enter API Key or Request for one!</li>';
}

//check Max results in sidebar
var check_max = $('#real_max_results').val();
if(check_max==0){
error_message+='<li>Max results in sidebar is empty! Please enter a value!</li>';
}

//check whether got error message, if there is, display it!
if(error_message!=""){
$('#error_dialog').empty();
$('#error_dialog').html('<div><br/><ol>'+error_message+'</ol></div>');
$("#error_dialog").dialog("destroy");
$("#error_dialog").dialog({
	modal: true,
	title: '<span class="ui-icon ui-icon-alert" style="float:left; margin:2px 5px 0px 0px;"></span>Attention! There are problems with the following field(s),',
	width:500,
	buttons: {
				Ok: function() {
					$(this).dialog('close');
				}
			}

});
$('#keys_ajax_indicator').hide();
}else{
// if no error message!

//hide the dialog box
$('#error_dialog').hide();

//concatenate post data string!
var postdata = 'update_apikeys=yes&_wpnonce=$real_nonce';
    postdata += '&apikey='+check_api_key+'&maxresult='+check_max+'';
	
//post values to process_admin_ajax.php using jQuery AJAX!
$.ajax({
	   url: '$real_ajax_url',
	   type: 'Post',
	   cache: false,
	   data: postdata,
	   success: function(data) {
			  $('#error_dialog').empty();
			  $('#error_dialog').append(data);
			  $("#error_dialog").dialog("destroy");
			  $("#error_dialog").dialog({
					modal: true,
					title: 'Success! All API keys updated!',
					buttons: {
								Ok: function() {
									$(this).dialog('close');
								}
							}

				});
			  //hide ajax indicator!
              $('#keys_ajax_indicator').hide();
			  }
	 });


}//end else
		  
}//end of function
</script>


<script type="text/javascript">
 
function real_update_location_values(){

//show ajax indicatior!
$('#location_ajax_indicator').show();

//do form validation first!

var error_message = "";//declare global error message string!

//validation sortable location type and value!
var order = $('#real_metro_ajax_response').sortable('toArray');
var loca = "";
var loca_value = "";

//get value from sortable
for(i=0;i<order.length;i++){
var temp_loca = document.getElementById('real_location_type'+order[i]).value;
var temp_loca_value = document.getElementById('real_location_value'+order[i]).value;
  if(temp_loca_value==0){
  temp_loca = temp_loca.substr(0,1).toUpperCase()+temp_loca.substr(1);
  error_message+='<li>Location Type: '+temp_loca+' has no value! Please enter location value!</li>'; 
  }else{
   //concatenate into string with colon, so that can be posted over to process_admin_ajax.php 
   //for processing into array and update into options table
   loca += temp_loca+':';
   loca_value += temp_loca_value+':';
  }
}

//check whether got error message, if there is, display it!
if(error_message!=""){
$('#error_dialog').empty();
$('#error_dialog').html('<div><br/><ol>'+error_message+'</ol></div>');
$("#error_dialog").dialog("destroy");
$("#error_dialog").dialog({
	modal: true,
	title: '<span class="ui-icon ui-icon-alert" style="float:left; margin:2px 5px 0px 0px;"></span>Attention! There are problems with the following field(s),',
	width:500,
	buttons: {
				Ok: function() {
					$(this).dialog('close');
				}
			}

});
$('#location_ajax_indicator').hide();
}else{
// if no error message!

//hide the dialog box
$('#error_dialog').hide();

//concatenate post data string!
var postdata = 'update_locations=yes&_wpnonce=$real_nonce&location_type='+loca+'&location_value='+loca_value+'';
	
//post values to process_admin_ajax.php using jQuery AJAX!
$.ajax({
	   url: '$real_ajax_url',
	   type: 'Post',
	   cache: false,
	   data: postdata,
	   success: function(data) {
			  $('#error_dialog').empty();
			  $('#error_dialog').append(data);
			  $("#error_dialog").dialog("destroy");
			  $("#error_dialog").dialog({
					modal: true,
					title: 'Success! All locations updated!',
					width: 500,
					buttons: {
					    'Visit Site!': function() {
							window.open('$site_url','_blank');
						},
						'Ok': function() {
							$(this).dialog('close');
						}
						
					}

				});
			  //hide ajax indicatior!
              $('#location_ajax_indicator').hide();
			  }
	 });


}//end else
		  
}//end of function
</script>


<script type="text/javascript">
 
function real_update_custom_css(){

//show ajax indicator
$('#custom_ajax_indicator').show();

//do form validation first!

var error_message = "";//declare global error message string!

//check API key
var check_css = $('#real_custom_css').val();

//concatenate post data string!
var postdata = 'update_custom_css=yes&_wpnonce=$real_nonce';
    postdata += '&real_custom_css='+check_css+'';
	
//post values to process_admin_ajax.php using jQuery AJAX!
$.ajax({
	   url: '$real_ajax_url',
	   type: 'Post',
	   cache: false,
	   data: postdata,
	   success: function(data) {
			  $('#error_dialog').empty();
			  $('#error_dialog').append(data);
			  $("#error_dialog").dialog("destroy");
			  $("#error_dialog").dialog({
					modal: true,
					title: 'Success! Custom CSS Updated!',
					buttons: {
						Ok: function() {
							$(this).dialog('close');
						}
					}

				});			  
			  //hide ajax indicator
              $('#custom_ajax_indicator').hide(); 
			  }
	 });
		  
}//end of function
</script>

<script type="text/javascript">
//since version 2.1
//hide register form and show settings form
function real_hide_register_form(){
$('#realanswers_register_form').hide();
$('#settings_form').show();
}
</script>

<script type="text/javascript">
//since version 2.1
//show register form and hide settings form
function real_show_register_form(){
$('#realanswers_register_form').show();
$('#settings_form').hide();
}
</script>

<script type="text/javascript">
//since version 2.1
//Register form validation
function real_ajax_register_form(){

    $('#realanswers_register_form_errors').empty();
	//error mesaage
	var error_message = '';
	
	//retrieve role value
	
	var check_role = $('input:radio[name=role]:checked').val();
	
	//check for empty values
	var check_fname = $('#fname').val();

	
	var check_lname = $('#lname').val();

	
	var check_email = $('#email').val();
	var check_email2 = $('#email2').val();
	
	if(check_email!==check_email2){
	error_message += '<li>Email Address (retype) is different from Email Address!</li>';
	}
		
	if(error_message!==''){
	var show_error = '<div id="register_error_message" class="updated fade">';
	show_error += '<p><strong>The following needs your attention!</strong></p><ol>';
	show_error += error_message;
	show_error += '</ol></div>';
	$('#realanswers_register_form_errors').html(show_error);
	}
	
	if(error_message==''){
	//if pass form validation, register via ajax!
	$('#register_ajax_indicator').show();

		//concatenate post data string!
		var postdata = 'process_register_form=yes_process&_wpnonce=$real_nonce';
		postdata += '&fname='+check_fname+'&lname='+check_lname+'&email='+check_email+'&role='+check_role+'';
		 
		
		//post values to process_form.php using jQuery AJAX!
		$.ajax({
			   url: '$real_ajax_register_url',
			   type: 'Post',
			   cache: false,
			   data: postdata,
			   success: function(data) {
					  $('#realanswers_register_form_errors').empty();
					  $('#realanswers_register_form_errors').append(data);
					  $('#register_ajax_indicator').hide();
					  }
			 });
	
	}
}

//function to valicate email address
function validateEmail(elementValue){
   var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
   return emailPattern.test(elementValue);
 }
 
function real_ajax_register_api(){


}
</script>

<script type="text/javascript">
//script to highlight empty input found in admin settings <input>
//trigger by function found in admin settings <input> tag onchange='real_highlight_input()'
function real_highlight_input(){
	var check_api_key = $('#real_apikey').val();
	if(check_api_key==''){
    	$('#real_apikey').css('border','2px solid #CC3300');
	}else{
		$('#real_apikey').css('border','none');
	}
	
	var check_max_results = $('#real_max_results').val();
	if(check_max_results==''){
    	$('#real_max_results').css('border','2px solid #CC3300');
	}else{
		$('#real_max_results').css('border','none');
	}
	
	var check_location_value = $('#real_location_value0').val();
	if(check_location_value==''){
    	$('#real_location_value0').css('border','2px solid #CC3300');
	}else{
		$('#real_location_value0').css('border','none');
	}

}
$(document).ready(function(){
real_highlight_input();
});
</script>
 
END;
    echo"\n<!--End of RealAnswers Admin Scripts--->\n";
}


//create autocompleter script for location type already saved in options
function default_autocompleter() {

    $plugin_url = WP_PLUGIN_URL;

    $check_loca_count = get_option('real_location_count');

    if (!empty($check_loca_count)) {

        $real_location_typ = get_option("real_location_type");

        echo "\n<script type=\"text/javascript\">";

        echo"\n$().ready(function() {";

        for ($i = 0; $i <= $check_loca_count; $i++) {
            $real_location_type[$i] = $real_location_typ[$i];
            if (empty($real_location_type[$i])) {
                $real_location_type[$i] = "metro";
            }
            echo <<<END
real_default_autocomplete('$real_location_type[$i]','#real_location_value$i');
END;
        }
        //end for loop

        echo"\n});\n";
        echo "</script>\n";

    } else {

        echo <<<END
\n<script type="text/javascript">	
$().ready(function() {
var location = "metro";
$("#real_location_value0").autocomplete("$plugin_url/realanswers/location-value-json.php",{
	width:200,
	highlight:false,
	extraParams:{type:location}
	});
});
</script>\n
END;

    }

}

//end of function default_autocomplete()


//function to remove style tags stored in variable
function realanswers_remove_style_tags($arg) {
    $prefix = array("<style type=\"text/css\">", "<style type='text/css'>", "</style>");
    $remove_prefix = array(" ", " ", " ");
    $newcode = str_replace($prefix, $remove_prefix, $arg);
    return $newcode;
}

//hook custom css code to wp_head()
function realanswers_custom_css_in_head() {
    $code = get_option('real_custom_css');

    if (!empty($code)) {

        $cleaned_code = "<!--Start Custom Embedded Style by RealAnswers WordPress Plugin-->\n";
        $cleaned_code .= "<style type=\"text/css\" media=\"screen\">\n";
        $cleaned_code .= realanswers_remove_style_tags($code);
        $cleaned_code .= "\n</style>\n";
        $cleaned_code .= "<!--End of style-->\n";

        print $cleaned_code;

    }

}

add_action('wp_head', 'realanswers_custom_css_in_head');
?>
