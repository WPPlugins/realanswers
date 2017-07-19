<?php
/*
*Realanswers Sidebar Widget
*
*/
//add widget
add_action('widgets_init', 'load_realanswers_sidebar_widget');
//function to register widget
function load_realanswers_sidebar_widget() {
  register_widget('RealAnswersWidget');
}
class RealAnswersWidget extends WP_Widget {
  function RealAnswersWidget() {
    $widget_ops = array('classname' => 'realanswers',
      'description' => __('A Widget to get questions from and post questions to RealtyBaron\'s RealAnswers API  ', 'realanswers'));
    $control_ops = array('width' => 200, 'height' => 350, 'id_base' => 'realanswers');
    $this->WP_Widget('realanswers', __('RealAnswers Widget', 'realanswers'), $widget_ops, $control_ops);
  }
  function widget($args, $instance) {
    extract($args);
    $title = apply_filters('widget_title', $instance['title']);
    echo $before_widget;
    if ($title) {
      echo $before_title . $title . $after_title;
    }
    //check options API Key
    $check_key = get_option('real_apikey');
    //if api key not empty, proceed to do api call
    //else display error message
    if (!empty($check_key)) {
      //use realanswers rest api class to request xml response
      global $rsapi;
      //get location counts for checking
      $check_location_count = get_option('real_location_count');
      //check location counts, if more than zero, use multiple format if not use single format.
      if ($check_location_count > 0) {
        $multiple_setup = "true"; //show multiple set up!
      } else {
        $multiple_setup = "false"; //show single (old) set up!
      }
      if ($multiple_setup == "false") { //show old sidebar set up (single view with form)
        //get option values
        $real_location_value = get_option('real_location_value');
        $sidebar_location_value[0] = $real_location_value[0];
        $real_location_type = get_option('real_location_type');
        $sidebar_location_type[0] = $real_location_type[0];
        //check if value empty, if yes proceed to check others of not assign to $rsapi class
        if (!empty($sidebar_location_value[0])) {
          $sidebar_location_type = $sidebar_location_type[0];
          $sidebar_location_value = $sidebar_location_value[0];
        }
        //check empty location value
        //if all empty, print error message in sidebar!
        if (empty($real_location_value[0])) {
          echo "<p class='sidebar_error'>Please enter at least one location value!</p>";
        }
        $xml = $rsapi->sidebar_widget_question($sidebar_location_type, $sidebar_location_value);
        if (!empty($xml)) {
          //get status code 400 error 200 ok
          $status_code = $xml->status['code'];
          if ($status_code = "400") {
            $html = "<div class='realanswers_widget_response'>";
            //catch error message for status 400
            foreach ($xml->status->messages as $mess) {
              $errormessage = $mess->message;
              echo "<p class='sidebar_error'>" . $errormessage . "</p>";
            }
            $html .= "</div>";
            echo $html;
          }
          //end if($status_code = "400")
        }
        //end if(!empty($xml))
        if (!empty($xml)) {
          //check status code 200 is ok 400 is error
          if ($status_code = "200") {
            $html = "<div class='realanswers_widget_response'>";
            //url structure of answers
            $api_url_structure = get_bloginfo('url') . "/realanswers/answers";
            foreach ($xml->questions->question as $question) {
              $id = $question['id'];
              $answers = $question->answers;
              $title = $question->title;
              $html .= "<div class=\"sidebar_q_and_a\">";
              $html .= "<div class='sidebar_question'><a href='$api_url_structure?question_id=$id&type=$sidebar_location_type&value=$sidebar_location_value'>" . $title . "</a></div>";
              $html .= "<div class='sidebar_answers'>" . $answers . " answer(s)</div>";
              $html .= "</div>";
            }
            //start of mini form
            $action_url_raw = get_bloginfo('url') . "/realanswers/newquestions";
            $action_url = esc_url($action_url_raw);
            $html .= "<div class='sb_form_title'>Got a Real Estate Question?</div>";
            $html .= "<form action=\"$action_url\" method=\"post\" name=\"realanswers_sidebar_form\" id=\"realanswers_sidebar_form\">";
            //subject
            $html .= '<div id="sb_form_subject" class="sb_form_elements">';
            $html .= "<textarea name=\"subject\" id=\"sb_subject\"";
            $html .= "onfocus=\"this.value=(this.value=='Ask your question here....') ? '' : this.value;\" onblur=\"this.value=(this.value=='') ? 'Ask your question here....' : this.value;\">Ask your question here....</textarea>";
            $html .= "<input type='hidden' name='context' value='$sidebar_location_type'/>";
            $html .= "<input type='hidden' name='location' value='$sidebar_location_value'/>";
            $html .= '</div>';
            //submit button
            $html .= '<div id="sb_form_submit" class="sb_form_elements">';
            $html .= '<input type="hidden" name="process_sidebar_form" value="yes_process"/>';
            $html .= '<input type="submit" id="sb_form_submit_button" value="Post Question">';
            $html .= '</div>';
            //end of mini form
            $html .= "</form>";
            $all_question_url_structure = get_bloginfo('url') . "/realanswers/questions?";
            $all_question_url_structure .= "type=$sidebar_location_type&value=$sidebar_location_value";
            $html .= "<p class='sidebar_links'><a href='$all_question_url_structure'>View all Questions</a></p>";
            $html .= "<p class='sidebar_links'><a href='http://wordpress.org/extend/plugins/realanswers'>Powered By RealAnswers</a></p>";
            $html .= "</div>";
            echo $html;
          }
          //end if($status_code = "200")
        }
        //end if(!empty($xml))
      }
      //end if($multiple_setup == "false")
      if ($multiple_setup == "true") {
        //retrieve all option!
        $sb_location_count = get_option('real_location_count');
        $sb_location_value = get_option('real_location_value');
        $sb_location_type = get_option('real_location_type');
        //construct and echo out link!
        echo '<ul>';
        //echo out all link
        for ($j = 0; $j <= $sb_location_count; $j++) {
          $real_question_url_structure = get_bloginfo('url') . "/realanswers/questions?type=$sb_location_type[$j]&value=$sb_location_value[$j]";
          if (!empty($sb_location_value[$j])) {
            $result_html = "<li class='sidebar_links'><a href='$real_question_url_structure'>$sb_location_value[$j]</a></li>";
            echo $result_html;
          }
        }
        echo '</ul>';
        //print mini form if location type and location values
        //are posted from sidebar location list using $_GET to questions.php!
        if ($_GET['type']) {
          echo "<br/>"; //for new line spacing
          $sidebar_location_type = $_GET['type'];
          $sidebar_location_value = $_GET['value'];
          //start of mini form
          $action_url_raw = get_bloginfo('url') . "/realanswers/newquestions";
          $action_url = esc_url($action_url_raw);
          $html .= "<div class='sb_form_title'>Got a Real Estate Question?</div>";
          $html .= "<form action=\"$action_url\" method=\"post\" name=\"realanswers_sidebar_form\" id=\"realanswers_sidebar_form\">";
          //subject
          $html .= '<div id="sb_form_subject" class="sb_form_elements">';
          $html .= "<textarea name=\"subject\" id=\"sb_subject\"";
          $html .= "onfocus=\"this.value=(this.value=='Ask your question here....') ? '' : this.value;\" onblur=\"this.value=(this.value=='') ? 'Ask your question here....' : this.value;\">Ask your question here....</textarea>";
          $html .= "<input type='hidden' name='context' value='$sidebar_location_type'/>";
          $html .= "<input type='hidden' name='location' value='$sidebar_location_value'/>";
          $html .= '</div>';
          //submit button
          $html .= '<div id="sb_form_submit" class="sb_form_elements">';
          $html .= '<input type="hidden" name="process_sidebar_form" value="yes_process"/>';
          $html .= '<input type="submit" id="sb_form_submit_button" value="Post Question">';
          $html .= '</div>';
          //end of mini form
          $html .= "</form>";
          echo $html;
        }
        //for Styling purpose
        //if posted location type from mini form, echo powered link as <p class='sidebar_links'>
        //else echo as normal <p>
        if ($_GET['type']) {
          $powered_link .= "<p class='sidebar_links'><a href='http://wordpress.org/extend/plugins/realanswers'>Powered By RealAnswers</a></p>";
        } else {
          $powered_link .= "<p><a href='http://wordpress.org/extend/plugins/realanswers'>Powered By RealAnswers</a></p>";
        }
        echo $powered_link;
        //check empty location value
        //if all empty, print error message in sidebar!
        if (empty($sb_location_value)) {
          echo "<p class='sidebar_error'>Please enter at least one location value!</p>";
        }
      }
      //end if($multiple_setup == "true"){
    } else {
      echo "<p class='sidebar_error'>You must configure the RealAnswers plug-in first.</p>";
      echo "<p class='sidebar_error'>Please click on the following link and login to plugins settings page.</p>";
      $plugin_admin_url_structure = get_bloginfo('url') . "/wp-admin/options-general.php?page=realanswers_admin.php";
      echo "<a href='$plugin_admin_url_structure' class='sidebar_links'>RealAnswers Admin Settings</a>";
    }
    //end if(!empty($check_key))
    //lastly check if empty xml return from rest api class will indicate service unavailable
    //return Service is Unavailable message!
    if (empty($xml) && !empty($check_key) && !$multiple_setup == "true") {
      echo "<div class='sidebar_error'>Service is Unavailable</div>";
    }
    echo $after_widget;
  }
  //end of function widget
  function update($new_instance, $old_instance) {
    $instance = $old_instance;
    $instance['title'] = strip_tags($new_instance['title']);
    return $instance;
  }
  function form($instance) {
    $instance = wp_parse_args((array) $instance, array('title' => ''));
    $instance['title'] = strip_tags($instance['title']);

    ?>
    <p><label for="<?php echo $this->get_field_id('title'); ?>">Title: <input class="widefat"
        id="<?php echo $this->get_field_id('title'); ?>"
        name="<?php echo $this->get_field_name('title'); ?>"
        type="text"
        value="<?php echo $instance['title']; ?>"/></label>
    </p>

    <p>
    <?php

  }
}

?>