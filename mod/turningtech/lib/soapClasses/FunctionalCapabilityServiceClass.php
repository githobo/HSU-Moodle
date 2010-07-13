<?php
/*******
 * SOAP service class for FunctionalCapability services
 * @author jacob
 * 
 **/

// require the main library for this module
require_once(dirname(dirname(dirname(__FILE__))) . '/lib.php');

// require parent class
require_once(dirname(__FILE__) . '/AbstractSoapServiceClass.php');

class FunctionalCapabilityService extends SoapService {

  /**
   * get list of capabilities for user
   * @param $request
   * @return array of functionalCapabilityDto
   */
  public function getFunctionalCapabilities($request) {
    $user = NULL;
    $capabilities = NULL;
    
    $user = $this->authenticateRequest($request);
    return $this->service->getUserCapabilities($user);
  }
}
?>