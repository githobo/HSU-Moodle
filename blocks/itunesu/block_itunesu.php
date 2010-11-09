<?php // $Id: block_itunesu.php,v 1.1 2008/06/06 19:08:50 mchurch Exp $

/**
 * Allows for podcasts from a subscribed iTunesU space to be loaded into a block for
 * access by course users. SSO is handled by the block and its configuration settings.
 * 
 * (Code modified from site_main_menu block).
 *
 * @author Open Knowledge Technologies
 * @author Akin Delamarre <adelamarre@oktech.ca>
 */

class block_itunesu extends block_base {
    function init() {
        $this->title = get_string('itunesutitle', 'block_itunesu');
        $this->version = 2008030400;
    }

    function specialization() {
        if (!isset($this->config->title)) {
            $this->config->title = '';
        }
        if (!isset($this->config->text)) {
            $this->config->text = '';
        }
        if (!empty($this->config->title)) {
            $this->title = $this->config->title;
        }
    }

    function get_content() {
        global $CFG, $USER;
        
        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = '';
        $this->content->footer = '';

        if (!isloggedin()) {
          return $this->content;
          print_error();
        }

        $this->content = new stdClass;
        $this->content->text = $this->instance_translate_config_data_to_links($this->config);
        $this->content->footer = '';

        return $this->content;
    }

/** Currently do not need this
    function instance_allow_config() {
        return true;
    }
*/

    /**
     * Seralized the array of link captions and
     * destinations and call parent class method
     * to save the data
     */
    function instance_config_save($data) {
      $newdata = serialize($this->instance_convert_config_data($data));
      $data->text = $newdata;
      $jumk = $this->instance_revert_config_data($data);
      
      // And now forward to the default implementation defined in the parent class
      return parent::instance_config_save($data);
    }
    
    /**
     * Convert the data from a human readable
     * to an array of link captions and destinations.
     */
    function instance_convert_config_data($data) {
        $result = array();
        $i = 0;
        $caption = '';
        $destination = '';
        $linkinfo = array();
       
        if (isset($data->text) and !empty($data->text)) {

            $links = explode("\n", $data->text);
            
            foreach($links as $key => $tmp) {
                $links[$key] = rtrim($tmp, "\t\r\n ");
                
                if (!empty($tmp)) {
                    $linkinfo = explode(",", $tmp);
                    if (!empty($linkinfo)) {
                      list($caption, $destination) = $linkinfo;
                      $result[$i]['caption'] = $caption;
                      $result[$i]['destination'] = $destination;
                      $i++;
                    }
                }
  
            }

        }
        
        return $result; 
    }
    
    /**
     * Revert the data back to a human readable
     * format ex (<link caption>,<destination number>)
     */
    function instance_revert_config_data($data) {

        if (!empty($data->text)) {
        $newdata = unserialize($data->text);
        } else {
            $newdata = array();
        }
        $result = '';
        
        foreach($newdata as $key => $link) {
            $result .= $link['caption'] . "," . $link['destination'] . "\n";
        }
        
        return $result;
    }
    
    /**
     * Translate the formatted data to anchor links
     * for this instance of the block
     */
    function instance_translate_config_data_to_links($data) {
        global $CFG, $USER;

        if (!empty($data->text)) {
        $newdata = unserialize($data->text);
        } else {
            $newdata = array();
        }

        $result = '';
        
        foreach($newdata as $key => $link) {
            $result .= '<a href="'. $CFG->wwwroot . '/blocks/itunesu/itunesu_redirect.php?destination='. $link['destination'] .'" target="_blank">'.$link['caption'].'</a><br />';
        }
        
        return $result;
    }
    
    function instance_allow_multiple() {
        return true;
    }

    function has_config() {
        return true;
    }
}
?>