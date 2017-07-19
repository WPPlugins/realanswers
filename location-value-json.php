<?php
/*
*This file uses curl to get a JSON response of location values from the Realtybaron API
*The JSON response is parsed into HTML List for scriptaculous autocompleter.
*/
	//include wp-config
	$root = dirname(dirname(dirname(dirname(__FILE__))));
	if (file_exists($root.'/wp-load.php')) {
	// WP 2.6
	require_once($root.'/wp-load.php');
	} else {
	// Before 2.6
	require_once($root.'/wp-config.php');
	}

	//type parameter, excepts metro, city, zipcode, address.
	$type =  $_GET["type"];
	
	$q = $_GET['q'];

	//get api key from options table
	$real_api_keyy = get_option('real_apikey');
	
	//check api key
	if(empty($real_api_keyy)){
	//if empty stop doing api request!
	//show error message!
	echo"<script type=\"text/javascript\">";
	echo"$('#error_dialog').empty();";
	echo"$('#error_dialog').dialog('destroy');";
	echo"$('#error_dialog').html('<p><span class=\"ui-icon ui-icon-alert\" style=\"float:left; margin:0 7px 20px 0;\"></span>Please update API Keys before filling up location values!</p><p><span class=\"ui-icon ui-icon-alert\" style=\"float:left; margin:0 7px 20px 0;\"></span>Location value Auto Completer will not work without API Key!</p>');";
	echo"$('#error_dialog').dialog({modal: true,title: 'Attention!',buttons: { Ok: function() {									$(this).dialog('close');}}});";
	echo"</script>";
	echo"Required API Key!";
	die();
	}
	
	// create curl resource
    $ch = curl_init();
    // set url
    curl_setopt($ch, CURLOPT_URL, "http://api.realtybaron.com/answers/json/location/find/$type?location=$q&api_key=$real_api_keyy");
    //return the transfer as a string
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // $output contains the output string
    $output = curl_exec($ch);
    // close curl resource to free up system resources
    curl_close($ch);

    //use json_decode to decode json into objects 
	//and create a html list for autocompleter javascript to use.
    $obj = json_decode($output);
	if(!empty($obj->locations->location)){
		foreach($obj->locations->location as $loco){
		echo $loco."\n";
		}
	}
?>  