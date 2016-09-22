<?php
namespace Telus\Digital\Libraries\DarkLaunch\Implementations;

use Telus\Digital\Libraries\DarkLaunch\Interfaces\DarkLaunchInterface;

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
  * @var array
  * Default values for dark launching
  * e.g ["feature_1" => ["type" => "boolean", "value" => TRUE],
  *      "feature_2" => ["type" => "percentage", "value" => 30]];
  */
  protected $config;
  
  const DARK_LAUNCH_NAMESPACE = 'dark-launch';

  public function __construct(\Redis $redisConnection)
  {
    $this->redis = $redisConnection;
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
    $pipe = $this->redis->multi(Redis::PIPELINE);
    foreach($features_list as $feature){
      $pipe->hgetall("{$this->featureNamespace()}:feature:{$feature}");
    }
    $feature_data = $pipe->exec();

    $features = [];
    foreach($features_list as $key => $feature){
      $features[$feature] = $feature_data[$key];
    }
    return $features;
  }


  public function feature($feature_name) {
    $dark_launch_feature = $this->redis->hgetall("{$this->featureNamespace()}:feature:{$feature_name}");

    if(!$dark_launch_feature){
      $this->set_from_config($feature_name);
      $dark_launch_feature = $this->redis->hgetall("{$this->featureNamespace()}:feature:{$feature_name}");
    }
    return $dark_launch_feature ? $dark_launch_feature : $this->_return_error($feature_name);
  }


  public function enableFeature($feature_name, $feature_values) {
    if(!is_array($feature_values)){
      $feature_values = (array)$feature_values;
    }
    $multi = $this->redis->multi();
    $multi->hmset("{$this->featureNamespace()}:feature:{$feature_name}", $feature_values);
    $multi->sadd("{$this->featureNamespace()}:features", $feature_name);
    $multi->exec();
  }


  public function disableFeature($feature_name) {
    $multi = $this->redis->multi();
    $this->redis->del("{$this->featureNamespace()}:feature:{$feature_name}");
    $this->redis->srem("{$this->featureNamespace()}:features", $feature_name);
    $multi->exec();
  }

  public function parse($feature) {
    if(is_array($feature)){
      $type = ucfirst($feature['type']);
      return $this->{'parse'.$type}($feature);
    } else {
      return FALSE;
    }
  }
  

  public function parseBoolean($feature) {
    if(!isset($feature['value'])){
      throw new Exception('Invalid dark launch config: missing feature value');
    }
    return filter_var($feature['value'], FILTER_VALIDATE_BOOLEAN);
  }


  public function parseTime($feature) {
    if(!isset($feature['start']) OR !isset($feature['stop'])){
      throw new Exception('Invalid dark launch config: missing feature start and stop time');
    }

    if(!is_null($feature['stop']) AND ($feature['stop'] < $feature['start'])){
      error_log('Invalid value for stop time.', 0);
      return FALSE;
    }

    if($this->timeIsValid($feature['start'], $feature['stop'])){
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


  public function parsePercentage($feature) {
    if(!isset($feature['value'])){
      throw new Exception('Invalid dark launch config: missing feature value.');
    }

    $percentage = $feature['value'];
    if($percentage < 0 OR $percentage > 100) {
      error_log('Dark launch percentage is not in the 0 - 100 range.', 0);
      return FALSE;
    }

    $random_number = rand(0, 100);
    return $random_number <= $percentage ? TRUE : FALSE;
  }

  public function parseInt($feature) {
    if(!isset($feature['value'])){
      throw new Exception('Invalid dark launch config: missing feature value.');
    }

    $value = intval($feature['value']);
    return $value;
  }

  /**
  * Returns the string value
  * @param $feature array - An associative array of the features attributes
  * @return string
  */
  public function parseString($feature) {
    if(!isset($feature['value'])){
      throw new Exception('Invalid dark launch config: missing feature value.');
    }

    return $feature['value'];
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
  protected function setFromConfig($feature_name)
  {
    //add set members for user & project
    $this->_add_redis_set_members();
    
    $features = $this->config;
    if(isset($features)){
      if(is_array($features)){
        if(isset($features[$feature_name])){
          $this->enable_feature($feature_name, $features[$feature_name]);
        }
      }
    }
  }

  //////////////////////
  ////// PRIVATE ///////
  //////////////////////

  
  /**
  * Logs and error and returns false
  * @return boolean FALSE
  */
  private function _return_error($feature)
  {
    error_log("No dark launch value exists for: {$feature}", 0);
    return FALSE;
  }


  /**
  * Adds project and user to a sets that can be easily accessed
  */
  private function _add_redis_set_members()
  {
    $this->redis->sadd($this->projectNamespace().":projects", $this->project);
    $this->redis->sadd($this->userNamespace().":users", $this->user);
  }


  /**
  * Sets private variables
  * @param $params array - An assocative array to set class variables
  */
  private function _set_vars($params)
  {
    foreach($params as $key => $value){
      $this->{$key} = $params[$key];
    }
  }
}