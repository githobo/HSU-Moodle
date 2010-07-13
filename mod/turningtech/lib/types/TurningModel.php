<?php
/**
 * 
 * @author jacob
 *
 */
abstract class TurningModel {
  
  // DB fields used by all models
  protected $id;
  protected $created;
  // keeps track of whether the model needs saving
  protected $save;
  // stores child classes' DB table
  protected $tablename;
  
  /**
   * abstract constructor
   * @return unknown_type
   */
  public function __construct() {
    $save = FALSE;
  }
  
  
  /**
   * fetches instances of child classes
   * @param $table
   * @param $classname
   * @param $params
   * @return unknown_type
   */
  protected static function fetchHelper($table, $classname, $params) {
    $wheresql = array();
    foreach($params as $field=>$value) {
      $wheresql[] = (is_null($value) ? " $field IS NULL " : " $field = $value ");
    }
    if(empty($wheresql)) {
      $wheresql = '';
    }
    else {
      $wheresql = implode('AND', $wheresql);
    }
    
    if($data = get_record_select($table, $wheresql)) {
      $instance = new $classname();
      TurningModel::setProperties($instance, $data);
      return $instance;
    }
    else {
      return FALSE;
    }
  }
  
	/**
   * sets object fields
   * @param $instance
   * @param $properties
   * @return unknown_type
   */
  protected static function setProperties(&$instance, $properties) {
    $properties = (array) $properties;
    foreach($properties as $field=>$value) {
      $instance->$field = $value;
    }
  }
  
	/**
   * generator function
   * @param $params
   * @return unknown_type
   */
  protected static function generateHelper($classname, $params) {
    $instance = new $classname();
    $params = (array) $params;
    $params['saved'] = FALSE;
    TurningModel::setProperties($instance, $params);
    return $instance;
  }
  
	/**
   * all-purpose set function
   * @param $fieldname
   * @param $value
   * @return unknown_type
   */
  public function setField($fieldname, $value) {
    $this->$fieldname = $value;
    $this->saved = FALSE;
  }
  
  /**
   * all-purpose getter function
   * @param $fieldname
   * @return unknown_type
   */
  public function getField($fieldname) {
    return $this->$fieldname;
  }
  
  /**
   * get the id or return false if not yet saved
   * @return unknown_type
   */
  public function getId() {
    return (isset($this->id) ? $this->id : FALSE);
  }

  /**
   * saves the model to the database (insert and update)
   * @return unknown_type
   */
  public function save() {
    $result = FALSE;
    if(!$this->id) {
      $this->created = mktime();
      $result = insert_record($this->tablename, $this->getData());
      if($result) {
        $this->id = $result;
      }
    }
    else {
      $result = update_record($this->tablename, $this->getData());
    }
    $this->saved = ($result ? TRUE : FALSE);
    return $result;
  }
  
  
  /**
   * builds a DTO suitable for saving to the DB
   * @return unknown_type
   */
  abstract function getData();
  
  /**
   * builds the WHERE clause of a query from an array of fields
   * @param $params
   * @return unknown_type
   */
  public static function buildWhereClause($params) {
    $conditions = array();
    foreach($params as $field=>$value) {
      $conditions[] = "{$field} = {$value}";
    }
    return implode(' AND ', $conditions);
  }
}
?>