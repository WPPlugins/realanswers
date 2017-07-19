<?php
//The following process ajax data posted from realanswers admin settings

//include wp-config
$root = dirname(dirname(dirname(dirname(__FILE__))));
if (file_exists($root.'/wp-load.php')) {
// WP 2.6
require_once($root.'/wp-load.php');
} else {
// Before 2.6
require_once($root.'/wp-config.php');
}

/*******************The below process location values posted from admin********************/

/********************Update Options***************************/
if($_POST['update_apikeys'] == 'yes'){

$nonce_value = $_POST['_wpnonce'];

if (!wp_verify_nonce($nonce_value, 'realanswers_ajax_nonce') ) die('Failed Security check'); 

//process api key
$post_apikey = stripslashes($_POST['apikey']);
//process max result
$post_maxresult = stripslashes($_POST['maxresult']);

//update all values into option table!
update_option("real_apikey",$post_apikey);
update_option("real_max_results",$post_maxresult);

echo '<div>';
echo '<p><span class="ui-icon ui-icon-check" style="float:left;margin:0px 5px 0px 0px"></span>API Key</p>';
echo '<p><span class="ui-icon ui-icon-check" style="float:left;margin:0px 5px 0px 0px"></span>Max results in sidebar</p>';
echo '</div>';
}
/*************************End********************************/

/********************Update Options***************************/
if($_POST['update_locations'] == 'yes'){

$nonce_value = $_POST['_wpnonce'];

if (!wp_verify_nonce($nonce_value, 'realanswers_ajax_nonce') ) die('Failed Security check'); 

//process posted location type
$post_location_type = stripslashes($_POST['location_type']);//assign posted value
$remove_dashed_location_type = substr($post_location_type,0,-1);//removed dash from end of string 
$array_location_type = explode(":",$remove_dashed_location_type);//explode and assign to array for updated into options table

//process posted location value
$post_location_value = stripslashes($_POST['location_value']);
$remove_dashed_location_value = substr($post_location_value,0,-1); 
$array_location_value = explode(":",$remove_dashed_location_value);

//update all values into option table!
update_option("real_location_type",$array_location_type);
update_option("real_location_value",$array_location_value);

$location_count = get_option('real_location_count');

echo '<div>';
for($n = 0; $n <= $location_count; $n++){
$cap_location_type = ucfirst($array_location_type[$n]);
echo "<p><span class='ui-icon ui-icon-check' style='float:left;margin:0px 5px 0px 0px'></span> $cap_location_type - $array_location_value[$n]</p>";
}
echo '</div>';
}
/*************************End********************************/

/********************Update Options***************************/
if($_POST['update_custom_css'] == 'yes'){

$nonce_value = $_POST['_wpnonce'];

if (!wp_verify_nonce($nonce_value, 'realanswers_ajax_nonce') ) die('Failed Security check'); 

//assign posted value
$real_custom_css = stripslashes($_POST['real_custom_css']);
	 
// if no error proceed to update value into options database

//update post value into WordPress Database 
update_option('real_custom_css',$real_custom_css);
			 
			 
echo '<p><span class="ui-icon ui-icon-check" style="float:left; margin:0 7px 20px 0;"></span>Please view your blog for style updates to RealAnswers Plugin!</p>';
}
/*************************End********************************/


/*******************Delete Location********************/
if($_POST['delete_location'] == 'yes'){

$nonce_value = $_POST['_wpnonce'];

if (!wp_verify_nonce($nonce_value, 'realanswers_ajax_nonce') ) die('Failed Security check'); 

         //the following deletes location count	 
		 if(!empty($_POST['del_loca_count'])){
		 $del_location_count = get_option('real_location_count');
		 $del_new_location_count = $del_location_count - 1;
		 //update new count by minus 1
		 update_option('real_location_count',$del_new_location_count);
		 //the following deletes last array location valve from option
		 //assign array key to be deleted.
		 $del_count = $del_new_location_count;
		 //get value from option
		 $location_value = get_option('real_location_value');
		 //use unset function to remove array value according to key value,
		 //in this case its the last array
		 unset($location_value[$del_count]);
		 //update the remaining array back into option
		 update_option('real_location_value',$location_value);
		 
		 $location_type = get_option('real_location_type');
		 unset($location_type[$del_count]);
		 update_option('real_location_type',$location_type);
		 }//end if(!empty($_POST['del_location_count']))
		 


}//end if($_POST['delete_location'] == 'yes')

/*******************End********************/



/*******************Add Location********************/
if(isset($_POST['add_location'])== 'yes'){

$nonce_value = $_POST['_wpnonce'];

if (!wp_verify_nonce($nonce_value, 'realanswers_ajax_nonce') ) die('Failed Security check'); 
     
	 
//add location count	 
	 if(!empty($_POST['location_count'])){
	 $new_location_count = get_option('real_location_count')+1;
	 update_option('real_location_count',$new_location_count);

		$location_counts = get_option('real_location_count');
		if(!empty($location_counts)){
		
		    $count = $new_location_count;
			
			//sortable handle image url
            $handle_url =  WP_PLUGIN_URL.'/realanswers/arrow.png';

			//print out location value input response via ajax	
			
			echo"<li class='real_location_list' id='$count'><strong>Location:</strong>"; 

			echo"<select id='real_location_type$count' name='real_location_type$count' onChange='real_autocomplete(\"real_location_type$count\",\"#real_location_value$count\")'>";
			echo'<option value="metro"';
			if($real_location_type[$count] == 'metro'){echo 'selected="selected"';}
			echo'>Metro</option>';
			echo'<option value="city"';
			if($real_location_type[$count] == 'city'){echo 'selected="selected"';}
			echo'>City</option>';
			echo'<option value="zipcode"';
			if($real_location_type[$count] == 'zipcode'){echo 'selected="selected"';}
			echo'>ZIP code</option>';
			echo'<option value="address"';
			if($real_location_type[$count] == 'address'){echo 'selected="selected"';}
			echo'>Address</option>';
			echo'</select>';
				   
			echo"<input type='text' id='real_location_value$count' name='real_location_value$count' style='width:202px' value='$real_location_value[$count]'/>";
			echo"<img src='$handle_url' alt='move' width='16' height='16' title='Click to drag and sort!' class='handle' />";
			echo'</li>';
			
		
		    //print out javascript to run autocompleter javascript
		    $plugin_url = WP_PLUGIN_URL;

//heredox syntax			
echo <<<END
  <script type='text/javascript'>           
   real_add_autocomplete("#real_location_value$count");	
  </script>
END;
			
         }//end if(!empty($location_counts))

}//end if(!empty($_POST['location_count']))

}//end if(isset($_POST['add_location'])== 'yes')

/*******************End********************/
?>