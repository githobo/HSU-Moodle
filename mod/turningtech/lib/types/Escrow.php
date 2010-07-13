<?php
require_once(dirname(__FILE__) . '/TurningModel.php');

/**
 * handles transactions with the gradebook escrow
 * @author jacob
 *
 */
class Escrow extends TurningModel{
  
  // DB fields used only by this model
  protected $deviceid;
  protected $courseid;
  protected $itemid;
  protected $points_possible;
  protected $points_earned;
  protected $migrated;
  
  public $tablename = 'escrow';
  public $classname = 'Escrow';
  /**
   * constructor
   * @return unknown_type
   */
  public function __construct() {
    parent::__construct();
  }
  
  /**
   * fetch an instance
   * @param $params
   * @return unknown_type
   */
  public static function fetch($params) {
    if(isset($params['deviceid'])) {
      $params['deviceid'] = "'{$params['deviceid']}'";
    }
    return parent::fetchHelper('escrow', 'Escrow', $params);
  }
  
  /**
   * generator function
   * @param $params
   * @return unknown_type
   */
  public static function generate($params) {
    return parent::generateHelper('Escrow', $params);
  }
  
  
  /**
   * build a DTO for this escrow item
   * @return unknown_type
   */
  public function getData() {
    $data = new stdClass();
    $data->courseid = $this->courseid;
    $data->deviceid = $this->deviceid;
    $data->itemid = $this->itemid;
    $data->points_earned = $this->points_earned;
    $data->points_possible = $this->points_possible;
    $data->migrated = $this->migrated;
    if(isset($this->id)) {
      $data->id = $this->id;
    }
    if(isset($this->created)) {
      $data->created = $this->created;
    }
    return $data;
  }
  
}
?>