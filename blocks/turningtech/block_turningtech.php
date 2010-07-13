<?php
require_once($CFG->dirroot . '/mod/turningtech/lib.php');

class block_turningtech extends block_base {
  
  // maintain reference to integration service
  private $service;
  
  /**
   * set values for the block
   * @return unknown_type
   */
  function init() {
    $this->title = get_string('blocktitle', 'turningtech');
    $this->version = 2010050700;
    $this->service = new IntegrationServiceProvider();
  }
  
  /**
   * (non-PHPdoc)
   * @see docroot/blocks/block_base#specialization()
   */
  function specialization() {
    
  }
  
  /**
   * (non-PHPdoc)
   * @see docroot/blocks/block_base#get_content()
   */
  function get_content() {
    
    // if content is already set, just return it
    if($this->content !== NULL) {
      return $this->content;
    }
    
    // set up content
    $this->content = new stdClass();
    $this->content->text = '';
    $this->content->footer = '';
    
    // look up device ID
    global $CFG, $USER, $COURSE;
    // verify the user is a student in the current course
    if(MoodleHelper::isUserStudentInCourse($USER, $COURSE)) {
      $devicemap = TurningHelper::getDeviceIdByCourseAndStudent($COURSE, $USER);

      if($devicemap) {
        $link = $devicemap->displayLink();
        $this->content->text = get_string('usingdeviceid', 'turningtech', $link);
      }
      else {
        $this->content->text = get_string('nodeviceforthiscourse', 'turningtech');
        // get reminder pop-up messages
        $reminder = TurningHelper::getReminderMessage($USER, $COURSE);
        if(!empty($reminder)) {
          require_js(array('yui_yahoo', 'yui_event'));
          $this->content->footer .= self::popupCode($reminder);
        }
      }
      
      $this->content->footer .= "<a href='{$CFG->wwwroot}/mod/turningtech/index.php?id={$COURSE->id}'>"
        . get_string('managemydevices', 'turningtech') . "</a>\n";
        
      
    }
    elseif(MoodleHelper::isUserInstructorInCourse($USER, $COURSE)) {
      $this->content->text = "<a href='{$CFG->wwwroot}/mod/turningtech/index.php?id={$COURSE->id}'>"
        . get_string('manageturningtechcourse', 'turningtech') . "</a>\n";
    }
    
    
    if(!empty($this->content->text)) {
      $this->content->text .= "<link rel='stylesheet' type='text/css' href='{$CFG->wwwroot}/mod/turningtech/css/style.css'>";
    }
    return $this->content;
  }
  
  /**
   * generate the javascript that creates the popup
   * @param $message
   * @return unknown_type
   */
  static function popupCode($message) {
    $output =<<<EOF
<script type="text/javascript">
function showReminder() {
	alert('{$message}');
}

YAHOO.util.Event.onDOMReady(showReminder);
</script>
EOF;
    return $output;
  }
  
  /**
   * (non-PHPdoc)
   * @see docroot/blocks/block_base#instance_allow_config()
   */
  function instance_allow_config() {
    return FALSE;
  }
}
?>