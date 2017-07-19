<?php
/**
* Simple PHP class for REST API calls to RealtyBaron Real Answers API
*/

class realanswersapi{

//api key
var $rs_apikey;

//max result from wordpress admin option
var $rs_max_results;

//location type selected in wordpress admin
var $rs_location_type;

//location value entered in wordpress admin
var $rs_location_valve;

//location value2 entered in wordpress admin
var $rs_location_valve2;
      
		  //class constructor
		  function realanswersapi(){
		  
		  //get api key from wordpress admin options
		  $this->rs_apikey = get_option('real_apikey');
		  
		  //get max_results from wordpress admin options
		  $this->rs_max_results = get_option('real_max_results');
		
		 		  
		  //to get location type from wordpress option
		  //$this->rs_location_type = get_option('real_Location_type');
		  
	 
		  //get location value from wordpress admin option
		  //$raw_location_value = get_option('real_location_value');
		  
		  //replace string to lowercase
		  //$lowercase_location_value = strtolower($raw_location_value);
		  
	  	  //$this->rs_location_value = $lowercase_location_value;
		  
  
		  }
		  
		  //default sidebar question api call
		  function sidebar_widget_question($location_type,$location_value){
		  
		  $request_url  = "http://api.realtybaron.com/answers/rest/questions/find/";
		  $request_url .= "$location_type?location=$location_value";
		  $request_url .= "&start_index=0&max_results=$this->rs_max_results&api_key=$this->rs_apikey";
		  
		  $request_url = urlencode($request_url);
                
				//Adding the "@" symbol in front of any function call will suppress any
                //PHP-generated error messages from that function call.  			
		  		$response_xml = @simplexml_load_file($request_url); 
				//check xml structure, it should return a status code
				//if not, do not return xml to sidebar widget.
				//Checking of structure will ensure that 404 or 503 from Apache Server, which is html page 
				//will not pass through to sidebar widget and cause xml parsing error
				if (!empty($response_xml->status['code'])) {
	                      return $response_xml;
                   }  
		  
		  }
		
		  //normal question api call
		  function question($location_type,$location_value,$start_no,$max_res){
		  
		  $request_url  = "http://api.realtybaron.com/answers/rest/questions/find/";
		  $request_url .= "$location_type?location=$location_value";
		  $request_url .= "&start_index=$start_no&max_results=$max_res&api_key=$this->rs_apikey";
		  
		  $request_url = urlencode($request_url);
				
				$response_xml = @simplexml_load_file($request_url);
				if (!empty($response_xml->status['code'])) {
	                      return $response_xml;
                   }
		  }
			  
			//normal answer api call
			function answer($question_id){
			
			$ip = $_SERVER["REMOTE_ADDR"];
			$user_agent = $_SERVER["HTTP_USER_AGENT"];
		    $user_agent = urlencode($user_agent);
		   
			$request_url = "http://api.realtybaron.com/answers/rest/answers/get/$question_id?payload=html&user_agent=$user_agent&ip_address=$ip&api_key=$this->rs_apikey";
			
			//$request_url = urlencode($request_url);
	
				$response_xml = @simplexml_load_file($request_url);
				if (!empty($response_xml->status['code'])) {
	                      return $response_xml;
                   }
		   }
				 
			   
		   	//normal answer api call with sort
			function answer_sort($question_id,$order){

			$ip = $_SERVER["REMOTE_ADDR"];
			$user_agent = $_SERVER["HTTP_USER_AGENT"];
			$user_agent = urlencode($user_agent);			
		   
			$request_url = "http://api.realtybaron.com/answers/rest/answers/get/$question_id?payload=html&user_agent=$user_agent&ip_address=$ip&api_key=$this->rs_apikey&order=$order";
			
			//$request_url = urlencode($request_url);
			
			    $response_xml = @simplexml_load_file($request_url);
				if (!empty($response_xml->status['code'])) {
	                      return $response_xml;
                   }
		   }
		   
		   //redo api call
			function redo($sid){
		   
			$request_url = "http://api.realtybaron.com/answers/rest/question/edit/$sid?api_key=$this->rs_apikey";
			
			$request_url = urlencode($request_url);
		
				$response_xml = @simplexml_load_file($request_url);
				if (!empty($response_xml->status['code'])) {
	                      return $response_xml;
                   }
		   }
		   
		   //use php curl extension to post data to realtybaron post api and parse xml response.
		   function post($context,$postdata){
		   $rs_ch = curl_init("http://api.realtybaron.com/answers/rest/question/post/$context");
		   //$rs_ch = curl_init("http://testurl.com/realtybaron/wp-content/plugins/realanswers/status-400.php");
		   curl_setopt($rs_ch, CURLOPT_POST, 1);
		   curl_setopt($rs_ch, CURLOPT_POSTFIELDS ,$postdata);
		   curl_setopt($rs_ch, CURLOPT_FOLLOWLOCATION ,1);
		   curl_setopt($rs_ch, CURLOPT_HEADER ,0);  // DO NOT RETURN HTTP HEADERS
		   curl_setopt($rs_ch, CURLOPT_RETURNTRANSFER ,1);  // RETURN THE CONTENTS OF THE CALL
		   //curl_setopt($rs_ch, CURLOPT_TIMEOUT, 20);//set time out 
		   $res = curl_exec($rs_ch);
		   curl_close($rs_ch);
		   return $res;
		   }
		   
		   
		   //use php curl extension to post data to realtybaron add agent api and parse xml response.
		   function register_api_key($role,$postdata){
		   $rs_ch = curl_init("http://api.realtybaron.com/answers/rest/user/add/$role");
		   //$rs_ch = curl_init("http://testurl.com/realtybaron/wp-content/plugins/realanswers/status-400.php");
		   curl_setopt($rs_ch, CURLOPT_POST, 1);
		   curl_setopt($rs_ch, CURLOPT_POSTFIELDS ,$postdata);
		   curl_setopt($rs_ch, CURLOPT_FOLLOWLOCATION ,1);
		   curl_setopt($rs_ch, CURLOPT_HEADER ,0);  // DO NOT RETURN HTTP HEADERS
		   curl_setopt($rs_ch, CURLOPT_RETURNTRANSFER ,1);  // RETURN THE CONTENTS OF THE CALL
		   //curl_setopt($rs_ch, CURLOPT_TIMEOUT, 20);//set time out 
		   $res = curl_exec($rs_ch);
		   curl_close($rs_ch);
		   return $res;
		   }
		   
		   
	   
		   
}//end of class

//set new realanswersapi class in $rsapi variable for later use
//example usage;
//global $rsapi
//$response = $rsapi->question('metro','austin-san+marcos,%20tx',0,2);

if ( ! isset($rsapi) ) {

	$rsapi = new realanswersapi;
}
?>