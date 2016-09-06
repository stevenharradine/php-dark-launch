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

  public function __construct($params = [])
  {
    $this->_set_vars($params);
    if(!isset($this->redis)){
      throw new \Exception('No redis connection set');
    }
  }


  public function feature_enabled($feature_name)
  {
    $feature_data = $this->feature($feature_name);
    return $this->parse($feature_data);
  }


  public function projects()
  {
    return $this->redis->smembers(self::DARK_LAUNCH_NAMESPACE.":projects");
  }


  public function users()
  {
    return $this->redis->smembers(self::DARK_LAUNCH_NAMESPACE.":project:{$this->project}:users");
  }


  public function features()
  {
    $features_list = $this->redis->smembers("{$this->feature_namespace()}:features");
    $pipe = $this->redis->multi(Redis::PIPELINE);
    foreach($features_list as $feature){
      $pipe->hgetall("{$this->feature_namespace()}:feature:{$feature}");
    }
    $feature_data = $pipe->exec();

    $features = [];
    foreach($features_list as $key => $feature){
      $features[$feature] = $feature_data[$key];
    }
    return $features;
  }


  public function feature($feature_name)
  {
    $dark_launch_feature = $this->redis->hgetall("{$this->feature_namespace()}:feature:{$feature_name}");

    if(!$dark_launch_feature){
      $this->set_from_config($feature_name);
      $dark_launch_feature = $this->redis->hgetall("{$this->feature_namespace()}:feature:{$feature_name}");
    }
    return $dark_launch_feature ? $dark_launch_feature : $this->_return_error($feature_name);
  }


  public function enable_feature($feature_name, $feature_values)
  {
    if(!is_array($feature_values)){
      $feature_values = (array)$feature_values;
    }
    $multi = $this->redis->multi();
    $multi->hmset("{$this->feature_namespace()}:feature:{$feature_name}", $feature_values);
    $multi->sadd("{$this->feature_namespace()}:features", $feature_name);
    $multi->exec();
  }


  public function disable_feature($feature_name)
  {
    $multi = $this->redis->multi();
    $this->redis->del("{$this->feature_namespace()}:feature:{$feature_name}");
    $this->redis->srem("{$this->feature_namespace()}:features", $feature_name);
    $multi->exec();
  }



  public function parse($feature){
    if(is_array($feature)){
      $type = $feature['type'];
      return $this->{'parse_'.$type}($feature);
    } else {
      return FALSE;
    }
  }
  

  public function parse_boolean($feature)
  {
    if(!isset($feature['value'])){
      throw new Exception('Invalid dark launch config: missing feature value');
    }
    return filter_var($feature['value'], FILTER_VALIDATE_BOOLEAN);
  }


  public function parse_time($feature)
  {
    if(!isset($feature['start']) OR !isset($feature['stop'])){
      throw new Exception('Invalid dark launch config: missing feature start and stop time');
    }

    if(!is_null($feature['stop']) AND ($feature['stop'] < $feature['start'])){
      error_log('Invalid value for stop time.', 0);
      return FALSE;
    }

    if($this->time_is_valid($feature['start'], $feature['stop'])){
      return TRUE;
    } else {
      return FALSE;
    }
  }


  public function time_is_valid($start, $stop)
  {
    if($start < time() AND is_null($stop)) {
      return TRUE;
    } elseif($start < time() AND $stop > time()) {
      return TRUE;
    } else {
      return FALSE;
    }
  }


  public function parse_percentage($feature)
  {
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


  //////////////////////
  ////// PROTECTED /////
  //////////////////////
  

  /**
  * Return a nice namespace for accessing a feature
  * @return string - The namespace for a feature
  */
  protected function feature_namespace()
  {
    return $this->user_namespace().":user:{$this->user}";
  }


  /**
  * Return a nice namespace for accessing users
  * @return string - The namespace for user
  */
  protected function user_namespace()
  {
    return $this->project_namespace().":project:{$this->project}";
  }


 /**
  * Return a nice namespace for accessing projects
  * @return string - The namespace for projects
  */
  protected function project_namespace()
  {
    return self::DARK_LAUNCH_NAMESPACE;
  }


  /**
  * Check if value exists in config and sets it in redis if it does
  * @return boolean FALSE
  */
  protected function set_from_config($feature_name)
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
    $this->redis->sadd($this->project_namespace().":projects", $this->project);
    $this->redis->sadd($this->user_namespace().":users", $this->user);
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