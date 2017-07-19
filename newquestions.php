<?php
//The following is codes for new question form
/*******************The below checks post from sidebar widget********************/
if (isset($_POST['process_sidebar_form']) == 'yes_process') {
  $subject = $_POST['subject'];
  $context = $_POST['context'];
  $location = $_POST['location'];
//grab the question subject of this page for use as <title>
//to filter wp_title, this function is declared on realanswers_function.php    
  grab_question_title_for_wp_title($subject);
//This is a fresh view, show blank form.
  $real_answer_form = create_realanswers_question_form('Ask a Question', $context, $location, $subject, '', '', '', '', '', '', '');
//output form to browser!
  return $real_answer_form;
}
//end if(isset($_POST['process_sidebar_form'])== 'yes_process')
/*******************The below gets data back to populate form if status code 400 error********************/
if (isset($_GET['status']) == 'redo') {
  $context = $_GET['context'];
  $location = $_GET['location'];
  $subject = $_GET['subject'];
  $body = $_GET['body'];
  $tag = $_GET['tag'];
  $notify_me = $_GET['notify_me'];
  $fname = $_GET['fname'];
  $lname = $_GET['lname'];
  $email = $_GET['email'];
  $status_message = $_GET['status_message'];
//grab the question subject of this page for use as <title>
//to filter wp_title, this function is declared on realanswers_function.php    
  grab_question_title_for_wp_title($subject);
//repopulate form
  $real_answer_form = create_realanswers_question_form('Ask a Question', $context, $location, $subject, $body, $tag, $notify_me, $fname, $lname, $email, $status_message);
//output form to browser!
  return $real_answer_form;
}
//This is a fresh view, show blank form.
$real_answer_form = create_realanswers_question_form('Ask a Question', '', '', '', '', '', '', '', '', '', $status_message);
//output form to browser!
return $real_answer_form;

//end of newquestions.php!
?>