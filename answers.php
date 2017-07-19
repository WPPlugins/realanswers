<?php

$rs_question_id = $_GET['question_id'];
//for mini form
$rs_location_type = $_GET['type'];
$rs_location_value = $_GET['value'];
if (isset($_GET['order'])) {
  $rs_order = $_GET['order'];
  $sort = 'true';
} else {
  $sort = 'false';
}
//verified $rs_question_id is a number
if (is_numeric($rs_question_id)) {
  global $rsapi;
  if ($sort == 'true') {
    $res_ans = $rsapi->answer_sort($rs_question_id, $rs_order);
  } elseif ($sort == 'false') {
    $res_ans = $rsapi->answer($rs_question_id);
  }
  //if empty response print service unavailable message
  if (empty($res_ans)) {
    $html = "<div class='sidebar_error'>Service is Unavailable</div>";
    return $html;
  }
  //proceed if check not empty response from api
  if (!empty($res_ans)) {
    //check that status is not 404 error
    $status_check = $res_ans->status['code'];
    if ($status_check == '404') {
      return "<p>Invalid request question id. Please try again.</p>";
    }
    $title = $res_ans->question->title;
    //grab the answer title of this page for use as <title>
    //to filter wp_title, this function is declared on realanswers_function.php
    grab_title_for_wp_title($title);
    $body = $res_ans->question->body;
    $html = "<div class='realanswers_answers'>";
    $html .= "<h2 class=\"answers_title\">" . $title . "</h2>";
    $html .= "<p class='answers_body'>" . $body . "</p>";
    foreach ($res_ans->links as $links) {
      for ($i = 0; $i < 4; $i++) {
        $link_rel[$i] = $links->link[$i]['rel'];
        //Display link if rel='canonical',
        //which indentify it as the source link, if not do not display!
        if ($link_rel[$i] == "canonical") {
          $link2 = '<a href="' . $links->link[$i]['href'] . '" rel="' . $links->link[$i]['rel'] . '" class="answers_link">' . $links->link[$i]['text'] . '</a>';
          $html .= "<br/>";
          $html .= $link2;
          $html .= "<br/><br clear=\"all\" />";
        }
        //answer this question button
        if ($link_rel[$i] == "response") {
          //$link3 = '<a href="'.$links->link[$i]['href'].'" rel="'.$links->link[$i]['rel'].'" class="answers_link">'.$links->link[$i]['text'].'</a>';
          $link3 = "<button onclick=\"window.location.href='" . $links->link[$i][href] . "'\">" . $links->link[$i]['text'] . "</button>";
          $html .= $link3;
          $html .= "<br/>";
        }
      }
      //end for loop
    }
    //end foreach
    //url structure to answers
    $ans_url_structure = get_bloginfo('url') . "/realanswers/answers";
    //check got answer or not
    $anss = $res_ans->answers->answer->content;
    if (!empty($anss)) {
      $html .= "<p class='answers_title'>Answers:</p>";
      $html .= "<div class='realanswers_sort'>";
      $html .= "Sort by:  ";
      if ($rs_order != 'revenue') {
        $html .= "<a href='$ans_url_structure?question_id=$rs_question_id&type=$rs_location_type&value=$rs_location_value&order=revenue' class='answers_sort_link'>Default</a> |";
      } else {
        $html .= "Default |";
      }
      if ($rs_order != 'ranking') {
        $html .= " <a href='$ans_url_structure?question_id=$rs_question_id&type=$rs_location_type&value=$rs_location_value&order=ranking' class='answers_sort_link'>Ranking</a> |";
      } else {
        $html .= " Ranking |";
      }
      if ($rs_order != 'rating') {
        $html .= " <a href='$ans_url_structure?question_id=$rs_question_id&type=$rs_location_type&value=$rs_location_value&order=rating' class='answers_sort_link'>Rating</a> |";
      } else {
        $html .= " Rating |";
      }
      if ($rs_order != 'recent') {
        $html .= " <a href='$ans_url_structure?question_id=$rs_question_id&type=$rs_location_type&value=$rs_location_value&order=recent' class='answers_sort_link'>Recent</a> |";
      } else {
        $html .= " Recent |";
      }
      if ($rs_order != 'oldest') {
        $html .= " <a href='$ans_url_structure?question_id=$rs_question_id&type=$rs_location_type&value=$rs_location_value&order=oldest' class='answers_sort_link'>Oldest</a> ";
      } else {
        $html .= " Oldest ";
      }
      $html .= "</div><br/>";
    }
    //end if (!empty($anss))
    foreach ($res_ans->answers->answer as $answer) {
      $content = $answer->content;
      $html .= $content;
    }
    $html .= "</div><br clear='both'/>";
  }
  //end if(!empty($res_ans))
  return $html;
}//end if
?>