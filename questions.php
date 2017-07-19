<?php

// Number of records to show per page:
$display = 10;
$location_type = $_GET['type'];
$location_value = $_GET['value'];

//in this plugin
//note that start is start page
//starts from 0 index == page 1
//not the normal start record number.
if (isset($_GET['s'])) {
	$start = $_GET['s'];
} else {
	$start = 0;
}

//use realanswers rest api class to request xml response
global $rsapi;

$xml = $rsapi->question($location_type,$location_value,$start,$display);

//if empty response print service unavailable message
if(empty($xml)){
$htm = "<div class='sidebar_error'>Service is Unavailable</div>";
return $htm;
}

//check that $xml response is returned from api
if(!empty($xml)){

if ($xml->status['code']=='400'){
$error_message=$xml->status->messages->message;
return "<p>$error_message</p><p>Please check your admin setting</p>";
}

//parse total number of records from response
$num_records = $xml->questions['total_results'];
	
// Determine how many pages there are. 
if (isset($_GET['np'])) { // Already been determined.

	$num_pages = $_GET['np'];

} else { // Need to determine.

	// Calculate the number of pages.
	if ($num_records > $display) {// More than 1 page.
		$num_pages = ceil ($num_records/$display);// use ceil to round up to nearest number.

    } else {
		$num_pages = 1;
	}
	
} // End of np IF.
		
		
		$htm = "<div class='realanswers_questions'>";
		
		$real_title = $location_value;
		
		//grab the location value of this page for use as <title>
        //to filter wp_title, this function is declared on realanswers_function.php            
        get_question_title($location_value);
 
		$htm .= "<h2 class='question_post_title'>Recent Questions in $real_title</h2>";
		
		//url structure to answers
		$ans_url_structure = get_bloginfo('url')."/realanswers/answers";
		
		$assign_answer_link = array();
		$assign_source_link = array();
		
		foreach ($xml->questions->question as $question) {
		$id = $question['id'];
		$answers = $question->answers;
		$title = $question->title;

		//print_r($xml);
		
		$htm .= "<h3 class='question_title'><a href='$ans_url_structure?question_id=$id&type=$location_type&value=$location_value'>".$title."</a></h3>";
		
		//use for loop to check links
		for($i=0;$i<4;$i++){
		
		$linktitle[$i] = $question->links->link[$i]['title'];
		$text[$i] = $question->links->link[$i]['text'];
		$rel[$i] = $question->links->link[$i]['rel'];
		$href[$i] = $question->links->link[$i]['href'];
		
        //if rel='response' echo as "Answer this question" link
		if($rel[$i]=='response'){
        $assign_answer_link = array($linktitle[$i],$text[$i],$rel[$i],$href[$i]);
		}//end if
		
		//if rel='canonical' echo as Source:Example.com
		if($rel[$i]=='canonical'){
        $assign_source_link = array($linktitle[$i],$text[$i],$rel[$i],$href[$i]);
		}//end if
		
		}//end for loop
		
		$htm .= "<p class='question_answer'>".$answers." Answers - <a href='$assign_answer_link[3]' rel='$assign_answer_link[2]' title='$assign_answer_link[0]' target='_blank' class='question_link'>$assign_answer_link[1]</a> - <a href='$assign_source_link[3]' rel='$assign_source_link[2]' title='$assign_source_link[0]' target='_blank' class='question_link'>$assign_source_link[1]</a></p>";
		
		}//end foreach
		


//url structure to questions
$q_url_structure = get_bloginfo('url')."/realanswers/questions";

		
// Make the links to other pages, if necessary.
if ($num_pages > 1) {

$htm.="<div class='realanswers-pagination'>";
	
	// Determine what page the script is on.	
	$current_page = $start;
	
	// If it's not the first page, make a Previous button.
	if ($current_page != 0) {
		$htm .= "<span class=\"previous realanswers-page-numbers\" ><a href=\"$q_url_structure?s=" . ($start - 1) ."&np=" . $num_pages . "&type=".$location_type."&value=".$location_value."\">Previous</a></span> ";
	}
	
	// Make all the numbered pages.
	for ($i = 0; $i < $num_pages; $i++) {
		if ($i != $current_page) {
			$htm .= "<span class=\"realanswers-page-numbers\" ><a href=\"$q_url_structure?s=" . $i . "&np=" . $num_pages . "&type=".$location_type."&value=".$location_value."\">" . ($i+1) . "</a></span> ";
		} else {
		    $htm .= '<span class="realanswers-page-numbers current">';
     		$htm .= ($i+1);
			$htm .= '</span>';
			$htm .= ' ';
		}
	}
	
	// If it's not the last page, make a Next button.
	if (($current_page+1) != $num_pages) {
		$htm .= "<span class=\"next realanswers-page-numbers\" ><a href=\"$q_url_structure?s=" . ($current_page+1) . "&np=" . $num_pages . "&type=".$location_type."&value=".$location_value."\">Next</a></span>";
	}
	
$htm.="</div>";

} // End of links section.

$htm .= "</div>";

}//end of if(!empty($xml))

return $htm;	
?>