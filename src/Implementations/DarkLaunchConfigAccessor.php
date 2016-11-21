<?php
namespace Telus\Digital\Libraries\DarkLaunch\Implementations;

use Telus\Digital\Libraries\DarkLaunch\Interfaces\DarkLaunchInterface;
use Illuminate\Database\Capsule\Manager as Capsule;

class DarkLaunchConfigAccessor implements DarkLaunchInterface
{

  /**
  * @var string
  * The name of the project which is dark launching
  */
  protected $project = 'global';

  /**
  * @var string
  * The name of the user who is dark launching
  */
  protected $user = 'global';

  /**
  * @var Redis
  * The redis connection we shall use
  */
  protected $redis;

  /**
  * @var Mysql connection
  * The mysql connection we shall use
  */
  protected $mysql;

  /**
   * Name of mysql table
   * @var String
   */
  protected $mysqlTableName = 'keys_to_values';

  /**
  * @var array
  * Default values for dark launching
  * e.g ["feature_1" => ["type" => "boolean", "value" => TRUE],
  *      "feature_2" => ["type" => "percentage", "value" => 30]];
  */
  protected $config;
  
  const DARK_LAUNCH_NAMESPACE = 'dark-launch';

  public function __construct(\Redis $redisConnection, Capsule $mysqlConnection=null, $intialConfig=[], $project='global', $user='global')
  {
    $this->redis = $redisConnection;
    $this->mysql = $mysqlConnection;
    $this->project = $project;
    $this->user = $user;
    $this->config = $intialConfig;
  }


  public function featureEnabled($featureName) {
    $featureData = $this->feature($featureName);
    return $this->parse($featureData);
  }


  public function projects() {
    return $this->redis->smembers(self::DARK_LAUNCH_NAMESPACE.":projects");
  }


  public function users() {
    return $this->redis->smembers(self::DARK_LAUNCH_NAMESPACE.":project:{$this->project}:users");
  }


  public function features() {
    $features_list = $this->redis->smembers("{$this->featureNamespace()}:features");
    $pipe = $this->redis->multi(\Redis::PIPELINE);
    foreach($features_list as $feature){
      $pipe->hgetall("{$this->featureNamespace()}:feature:{$feature}");
    }
    $feature_data = $pipe->exec();

    $features = [];
    foreach($features_list as $feature){
      $features[$feature] = $feature_data[$feature];
    }
    return $features;
  }


  public function feature($featureName) {
    $dark_launch_feature = $this->redis->hgetall("{$this->featureNamespace()}:feature:{$featureName}");

    if(!$dark_launch_feature){
      $this->setFromConfig($featureName);
      $dark_launch_feature = $this->redis->hgetall("{$this->featureNamespace()}:feature:{$featureName}");
    }
    return $dark_launch_feature ? $dark_launch_feature : $this->returnError($featureName);
  }


  public function enableFeature($featureName, $featureValues) {
    if(empty($featureValues)) {
      throw new \Exception('Empty value passed in for featureValues');
    }
    if(!is_array($featureValues)){
      $featureValues = (array)$featureValues;
    }
    $key = "{$this->featureNamespace()}:feature:{$featureName}";
    $multi = $this->redis->multi();
    $multi->hmset($key, $featureValues);
    $multi->sadd("{$this->featureNamespace()}:features", $featureName);
    $multi->exec();

    if(!is_null($this->mysql)) {
      $this->mysql->table($this->mysqlTableName)->insert([
        "key" => $key,
        "value" => json_encode($featureValues)
      ]);
    }
  }


  public function disableFeature($featureName) {
    $multi = $this->redis->multi();
    $this->redis->del("{$this->featureNamespace()}:feature:{$featureName}");
    $this->redis->srem("{$this->featureNamespace()}:features", $featureName);
    $multi->exec();
  }

  public function parse($featureValue) {
    if(is_array($featureValue)){
      $type = $featureValue['type'];
      
      $typeParts = explode('_', $type);
      if(count($typeParts) > 1) {
        foreach($typeParts as $index => $typePart) {
          $typeParts[$index] = ucfirst($typePart);
        }
        $type = implode('', $typeParts);
      }
      else {
        $type = ucfirst($type);
      }
      
      return $this->{'parse'.$type}($featureValue);
    } else {
      return FALSE;
    }
  }
  

  public function parseBoolean($featureValue) {
    if(!isset($featureValue['value'])){
      throw new Exception('Invalid dark launch config: missing feature value');
    }
    return filter_var($featureValue['value'], FILTER_VALIDATE_BOOLEAN);
  }


  public function parseTime($featureValue) {
    if(!isset($featureValue['start']) OR !isset($featureValue['stop'])){
      throw new Exception('Invalid dark launch config: missing feature start and stop time');
    }

    if(!is_null($featureValue['stop']) AND ($featureValue['stop'] < $featureValue['start'])){
      error_log('Invalid value for stop time.', 0);
      return FALSE;
    }

    if($this->timeIsValid($featureValue['start'], $featureValue['stop'])){
      return TRUE;
    } else {
      return FALSE;
    }
  }


  public function timeIsValid($start, $stop) {
    if($start < time() AND is_null($stop)) {
      return TRUE;
    } elseif($start < time() AND $stop > time()) {
      return TRUE;
    } else {
      return FALSE;
    }
  }


  public function parsePercentage($featureValue) {
    if(!isset($featureValue['value'])){
      throw new Exception('Invalid dark launch config: missing feature value.');
    }

    $percentage = $featureValue['value'];
    if($percentage < 0 OR $percentage > 100) {
      error_log('Dark launch percentage is not in the 0 - 100 range.', 0);
      return FALSE;
    }

    $random_number = rand(0, 100);
    return $random_number <= $percentage ? TRUE : FALSE;
  }

  public function parseInt($featureValue) {
    if(!isset($featureValue['value'])){
      throw new Exception('Invalid dark launch config: missing feature value.');
    }

    $value = intval($featureValue['value']);
    return $value;
  }


  public function parseString($featureValue) {
    if(!isset($featureValue['value'])){
      throw new Exception('Invalid dark launch config: missing feature value.');
    }

    return $featureValue['value'];
  }

  public function parseTimeString($featureValue){
    if(!isset($featureValue['start']) OR !isset($featureValue['stop'])){
      throw new Exception('Invalid dark launch config: missing feature start and stop time');
    }

    $result = false;

    if(!is_null($featureValue['stop']) AND ($featureValue['stop'] < $featureValue['start'])){
      error_log('Invalid value for stop time.', 0);
      $result = FALSE;
      return $result;
    }
    if($this->timeIsValid($featureValue['start'], $featureValue['stop'])
      && isset($featureValue['value'])){

      $result = $featureValue['value'];
    }

    return $result;
  }

  public function parseTrafficSource($featureValue) {
    $return = true;

    if(!isset($featureValue['value'])){
      throw new Exception('Invalid dark launch config: missing feature value');
    } else {
      $is_external_header = $_SERVER['is-external'];
      $is_external = isset($is_external_header) && $is_external_header;

      if ($featureValue['value'] == 'external') {
        $return = $is_external;
      } else if ($featureValue['value'] == 'internal') {
        $return = ! $is_external;
      }
    }
    return $return;
  }

  public function parseCookie($featureValue) {
    $cookie_name = &$featureValue['value'];
    if(!isset($cookie_name)){
      throw new Exception('Invalid dark launch config: missing feature value');
    }

    $cookie_value = &$_COOKIE[$cookie_name];
    if(!isset($cookie_value)) {
      return false;
    } else {
      return filter_var($cookie_value, FILTER_VALIDATE_BOOLEAN);
    }
  }

  //////////////////////
  ////// PROTECTED /////
  //////////////////////
  

  /**
  * Return a nice namespace for accessing a feature
  * @return string - The namespace for a feature
  */
  protected function featureNamespace()
  {
    return $this->userNamespace().":user:{$this->user}";
  }


  /**
  * Return a nice namespace for accessing users
  * @return string - The namespace for user
  */
  protected function userNamespace()
  {
    return $this->projectNamespace().":project:{$this->project}";
  }


 /**
  * Return a nice namespace for accessing projects
  * @return string - The namespace for projects
  */
  protected function projectNamespace()
  {
    return self::DARK_LAUNCH_NAMESPACE;
  }


  /**
  * Check if value exists in config and sets it in redis if it does
  * @return boolean FALSE
  */
  protected function setFromConfig($featureName)
  {
    //add set members for user & project
    $this->addRedisSetMembers();
    
    $features = $this->config;
    if(isset($features) 
    && is_array($features) 
    && isset($features[$featureName])){
      $this->enableFeature($featureName, $features[$featureName]);
    }
  }

  //////////////////////
  ////// PRIVATE ///////
  //////////////////////

  
  /**
  * Logs and error and returns false
  * @return boolean FALSE
  */
  private function returnError($feature)
  {
    error_log("No dark launch value exists for: {$feature}", 0);
    return FALSE;
  }


  /**
  * Adds project and user to a sets that can be easily accessed
  */
  private function addRedisSetMembers()
  {
    $this->redis->sadd($this->projectNamespace().":projects", $this->project);
    $this->redis->sadd($this->userNamespace().":users", $this->user);
  }

}