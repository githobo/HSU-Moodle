<?php
##############################################
#
# iTunes Authentication Class
#
# Written by Aaron Axelsen - axelsena@uww.edu
# University of Wisconsin - Whitewater
# Edited by Ryan Pharis, ryan.c.pharis@ttu.edu - Texas Tech University
#
# Class based on the Apple provided ITunesU.pl 
# example script.
#
# REQUIREMENTS:
#
#  PHP:
#  - tested with PHP 5.2
#  - make sure hash_hmac() works - http://us2.php.net/manual/en/function.hash-hmac.php
#  - php curl support
#
#
#Example Usage:
/*
<?php

include('itunes.php');
$itunes = new itunes();

// show loading screen while processing request
//include(ROOT_URL.'/includes/pages/itunes_load.php');

// Set User
$itunes->setUser("Jane Doe", "janedoe@example.edu", "jdoe", "42");

// Set Admin Permissions
$itunes->addAdminCredentials();

// Set Instructor Permission
//$itunes->addInstructorCredential('unique_name_from_itunes');

// Set Student Credential
//$itunes->addStudentCredential('unique_name_from_itunes');

// Set Handle
// This will direct login to the specific page
#$itunes->setHandle('');

// iTunes U Auth Debugging
$itunes->setDebug(true);

$itunes->invokeAction();

?>
*/
##############################################
class itunes {
    // Oktech - add
    var $authtoken;
    var $sitedomain;
    var $siteURL;
    var $debugSuffix;
    var $sharedSecret;
    var $administratorCredential;
    var $instructorCredential;
    var $studentCredential;
    var $urlonly;
    var $urlcredentials;
    var $destination;
    // Oktech
    
   /*
   * Create iTunes Object
   */
   public function __construct() {
      $this->setDebug(false);
      $this->siteURL = 'https://deimos.apple.com/WebObjects/Core.woa/Browse/example.edu';
      $this->sitedomain = 'example.edu';
      $this->directSiteURL = 'https://www.example.edu/cgi-bin/itunesu';
      $this->debugSuffix = '/abc1234';
      $this->sharedSecret = 'STRINGOFTHIRTYTWOLETTERSORDIGITS';
      $this->administratorCredential = 'Administrator@urn:mace:itunesu.com:sites:example.edu';
      $this->studentCredential = 'Student@urn:mace:itunesu.com:sites:example.edu';
      $this->instructorCredential = 'Instructor@urn:mace:itunesu.com:sites:example.edu';
      $this->credentials = array();
      // Set domain
      $this->setDomain();
   }
    // Oktech add
    public function getInstructorCredential() {
        return $this->instructorCredential;
    }
    
    public function setInstructorCredential($credential) {
        $this->instructorCredential = $credential;
    }

    public function getStudentCredential() {
        return $this->studentCredential;
    }
    
    public function setStudentCredential($credential) {
        $this->studentCredential = $credential;
    }

    public function getAdminCredential() {
        return $this->administratorCredential;
    }
    
    public function setAdminCredential($credential) {
        $this->administratorCredential = $credential;
    }
 
    public function getSharedSecret() {
        return $this->sharedSecret;
    }
    
    public function setSharedSecret($sharedsecret) {
        $this->sharedSecret = $sharedsecret;
    }

    public function getAuthToken() {
        return $this->authtoken;
    }

    public function setAuthToken($authtoken) {
        $this->authtoken = $authtoken;
    }

    public function getDebugSuffix() {
        return $this->debugSuffix;
    }

    public function setDebugSuffix($debugsuffix) {
        $this->debugSuffix = $debugsuffix;
    }

    public function getDirectSiteURL() {
        return $this->directSiteURL;
    }

    public function setDirectSiteURL($directsiteurl) {
        $this->directSiteURL = $directsiteurl;
    }

    public function getSiteDomain() {
        return $this->sitedomain;
    }

    public function setSiteDomain($sitedomain) {
        $this->sitedomain = $sitedomain;
    }

    public function getSiteURL() {
        return $this->siteURL;
    }
    
    public function setSiteURL($siteurl) {
        $this->siteURL = $siteurl;
    }
    
    /**
     * Extract the URL from the return html
     * block from the iTunes U server.  Replace
     * Apple's itmss tag with https
     */
    private function extractURL($htmlblock) {
        $remainder = '';
        $pos = 0;
        $result = '';
        
        $remainder = strstr($htmlblock, "_open('i");
        $remainder = substr_replace($remainder, '', 0, 7);
        $remainder = substr_replace($remainder, 'https', 0, 5);
        $pos = strpos($remainder, "');");
        $result = substr_replace($remainder, '', $pos);
        $this->urlonly = $result;
    }
    
    public function getExtractedURL() {
        return $this->urlonly;
    }

    /**
     * Extract the credentials part from the returned URL from
     * the iTunes U server
     */
    public function extractURLCredentials($url) {
        $result = '';
        $pos = 0;
        
        $remainder = strstr($url, $this->sitedomain.'?');
        $remainder = substr_replace($remainder, '', 0, strlen($this->sitedomain) + 1);
        $this->urlcredentials = $remainder;
    }
    
    public function getExtractedURLCredentials() {
        return $this->urlcredentials;
    }
    
    public function setDestination($destination) {
        $this->destination = $destination;
    }
    
    public function getDestination() {
        return $this->destination;
    }
    // Oktech add

   /*
   * Add's admin credentials for a given user
   */
   public function addAdminCredentials() {
      $this->addCredentials($this->administratorCredential);
   }

   /*
   * Add Student Credential for a given course
   */
   public function addStudentCredential($unique) {
      if ($unique) {
         $this->addCredentials($this->studentCredential.":$unique");
      } else {
         $this->addCredentials($this->studentCredential);
      }
   }

   /*
   * Add Instructor Credential for a given course
   */
   public function addInstructorCredential($unique) {
      if ($unique) {
         $this->addCredentials($this->instructorCredential.":$unique");
      } else {
         $this->addCredentials($this->instructorCredential);
      }
   }

   /*
   * Set User Information
   */
   public function setUser($name, $email, $netid, $userid) {
      $this->name = $name;
      $this->email = $email;
      $this->netid = $netid;
      $this->userid = $userid;
      return true;
   }

   /*
   * Set the Domain
   *
   * Takes the siteURL and splits off the destination, hostname and action path. 
   */
   private function setDomain() {
      $tmpArray = split("/",$this->siteURL);
      $this->siteDomain = $tmpArray[sizeof($tmpArray)-1];
      $this->actionPath = preg_replace("/https:\/\/(.+?)\/.*/",'$1',$this->siteURL); 
      $pattern = "/https:\/\/".$this->actionPath."(.*)/";
      $this->hostName = preg_replace($pattern,'$1',$this->siteURL);
      $this->destination = $this->siteDomain;
      return true;
   }

   /* 
   * Set the Handle
   *
   * Takes the handle as input and forms the get upload url string
   * This is needed for using the API to upload files directly to iTunes U
   */
   public function setHandle($handleIn) {
      $this->handle = $handleIn;
      $this->getUploadUrl = "http://deimos.apple.com/WebObjects/Core.woa/API/GetUploadURL/".$this->siteDomain.'.'.$this->handle;
      return true;
   }

   /*
   * Get Identity String
   *
   * Combine user identity information into an appropriately formatted string.
   * take the arguments passed into the function copy them to variables
   */
   private function getIdentityString() {
      # wrap the elements into the required delimiters.
      return sprintf('"%s" <%s> (%s) [%s]', $this->name, $this->email, $this->netid, $this->userid);
   }

   /*
   * Add Credentials to Array
   *  
   * Allows to push multiple credientials for a user onto the array
   */
   public function addCredentials($credentials) {
      array_push($this->credentials,$credentials);
      return true;
   }

   /*
   * Get Credentials String
   *
   * this is equivalent to join(';', @_); this function is present
      * for consistency with the Java example.
      * concatenates all the passed in credentials into a string
      * with a semicolon delimiting the credentials in the string.
   */
   private function getCredentialsString() {
      #make sure that at least one credential is passed in
      if (sizeof($this->credentials) < 1)
         return false;
      return implode(";",$this->credentials);
   }

   private function getAuthorizationToken() {
      # Create a buffer with which to generate the authorization token.
      $buffer = "";

      # create the POST Content and sign it
      $buffer .= "credentials=" . urlencode($this->getCredentialsString());
      $buffer .= "&identity=" . urlencode($this->identity);
      $buffer .= "&time=" . urlencode(mktime());

      # returns a signed message that is sent to the server
      $signature = hash_hmac('SHA256', $buffer, $this->sharedSecret);

      # append the signature to the POST content
      return sprintf("%s&signature=%s", $buffer, $signature);

   }

   /*
   * Invoke Action
   *
   * Send a request to iTunes U and record the response.
   * Net:HTTPS is used to get better control of the encoding of the POST data
   * as HTTP::Request::Common does not encode parentheses and Java's URLEncoder
   * does.
   */
   public function invokeAction() {

      $this->identity = $this->getIdentityString();
      $this->token = $this->getAuthorizationToken();

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $this->generateURL() . '?' . $this->token);
      //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
      //curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      
      // Oktech - change
      $this->authtoken = curl_exec($ch);
      curl_close($ch);
      
      /* Start a new sesstion and send a request for specific content with the appropriate credentials */
      $ch = curl_init();
      $this->extractURL($this->authtoken);
      $this->extractURLCredentials($this->urlonly);
      
      //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
      $destination_string = '';
      if ($this->destination != '') {
         $destination_string = '.' . $this->destination;
      }
      curl_setopt($ch, CURLOPT_URL, $this->siteURL . $destination_string . '?' . $this->urlcredentials);
      //curl_setopt($ch, CURLOPT_POST, 1);
      curl_exec($ch);
      
      curl_close($ch);
      // Oktech
   }

   /*
   * Auth and Upload File to iTunes U
   *
        * This method is said to not be as heavily tested by apple, so you may have 
   * unexpected results.
   *
   * $fileIn - full system path to the file you desire to upload
   */
   public function uploadFile($fileIn) {
                $this->identity = $this->getIdentityString();
                $this->token = $this->getAuthorizationToken();

      // Escape the filename
      $f = escapeshellcmd($fileIn);

      // Contact Apple and Get the Upload URL
      $upUrl = curl_init($this->getUploadUrl.'?'.$this->token);
      curl_setopt($upUrl, CURLOPT_RETURNTRANSFER, true);
      $uploadURL = curl_exec($upUrl);

      $error = curl_error($upUrl);
      $http_code = curl_getinfo($upUrl ,CURLINFO_HTTP_CODE);

      curl_close($upUrl);

                print $http_code;
                print "<br /><br />$uploadURL";
                if ($error) {
                   print "<br /><br />$error";
                }

      # Currently not working using php/curl functions.  For now, we are just going to echo a system command .. see below
      #// Push out the designated file to iTunes U
      #// Build Post Fields
      #$postfields = array("file" => "@$fileIn");

      #$pushUrl = curl_init($uploadURL);
      #curl_setopt($pushUrl, CURLOPT_FAILONERROR, 1);
      #curl_setopt($pushUrl, CURLOPT_FOLLOWLOCATION, 1);// allow redirects 
      #curl_setopt($pushUrl, CURLOPT_VERBOSE, 1);
      #curl_setopt($pushUrl, CURLOPT_RETURNTRANSFER, true);
      #curl_setopt($pushUrl, CURLOPT_POST, true);
      #curl_setopt($pushUrl, CURLOPT_POSTFILEDS, $postfields);
      #$output = curl_exec($pushUrl);
      #$error = curl_error($pushUrl);
      #$http_code = curl_getinfo($pushUrl, CURLINFO_HTTP_CODE);

      #curl_close($pushUrl);

      #print "<br/>";
      #print $http_code;
      #print "<br /><br />$output";
      #if ($error) {
      #   print "<br /><br />$error";
      #}

      // Set the php time limit higher so it doesnt time out.
      set_time_limit(1200);

      // System command to initiate curl and upload the file. (Temp until I figure out the php curl commands to do it)
      $command = "curl -S -F file=@$f $uploadURL";
   
      echo "<br/><br/>";
      echo $command;
      exec($command, $result, $error);
      if ($error) {
         echo "I'm busted";
      } else {
         print_r($result);
      }
      echo $command;
   }

   /*
   * Set Debugging
   *
   * Enable/Disable debugging of iTunes U Authentication
   */
   public function setDebug($bool) {
      if ($bool) {
         $this->debug = true;
      } else {
         $this->debug = false;
      }
      return true;
   }

   /*
   * Generate Site URL
   *
   * Append debug suffix to end of url if debugging is enabled
   */
   private function generateURL() {
      if ($this->debug) {
         return $this->siteURL.$this->getDebugSuffix();
      } elseif ($this->isHandleSet()) {
         return $this->directSiteURL.'.'.$this->handle;        
      } else {
         return $this->siteURL;
      }
   }

   /*
   * Check to see if the handle is set
   */
   private function isHandleSet() {
      if (isset($this->handle))
         return true;
      else
         return false;
   }
}
?>

