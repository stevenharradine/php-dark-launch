<?php
namespace Telus\Digital\Libraries\DarkLaunch\Interfaces;

interface DarkLaunchInterface {

  /**
   * Connection to redis
   * @param \Redis $redisConnection Connection to redis
   * @param Array $initialConfig The intial values of the config. They are sent in for lazy loading.
   * @param String $project The project that is using the DL library. This will used to build the namespace under which the keys are stored
   * @param String $user The user that is usig the DL library. This will used to build the namespace under which the keys are stored. Since many users might be sharing a dev env, we need this separation. 
   * @return
   */
  public function __construct(\Redis $redisConnection, $initialConfig=[], $project='global', $user='global');

  /**
  * Determines whether a dark launch feature is active
  * @param $feature string - The name of the feature
  * @return boolean - True if a feature is enabled, false if not
  */
  public function featureEnabled($featureName);

  /**
  * Get a list of projects
  * @return An arry containing the list of projects
  */
  public function projects();

  /**
  * Get a list of users
  * @return An arry containing a list of users for a project
  */
  public function users();

  /**
  * Get a list of features
  * @return An arry containing a list of features for a project and user
  */
  public function features();

  /**
  * Get a Dark Launch feature
  * @param $featureName string - The namespace when accessing redis
  * @return An array of values - The hash keys and values for the feature
  */
  public function feature($featureName);

  /**
  * Set a Dark Launch feature
  * @param $featureName string - The name of the feature
  * @param $featureValue array - An associative array of the features keys and values
  */
  public function enableFeature($featureName, $featureValues);

  /**
  * Disable a dark launch feature
  * @param $feature string - The name of the feature
  */
  public function disableFeature($featureName);

  /**
  * Parses a features value
  * @param $feature array - An associative array of a features attributes
  * @return boolean TRUE if feature is enabled
  */
  public function parse($feature);

  /**
  * Returns TRUE or FALSE based on the boolean value
  * @param $feature array - An associative array of the features attributes
  * @return boolean
  */
  public function parseBoolean($feature);

  /**
  * Returns TRUE when the current time is between start and stop time and FALSE otherwise
  * @param $feature array - A associative array of a features attributes
  * @return boolean TRUE if feature is enabled
  * Exception is thrown when stop time is before end time
  */
  public function parseTime($featureValue);

  /**
  * Returns TRUE when the current time is between start and stop time and FALSE otherwise
  * @param $start int - A unix time of start time
  * @param $stop int - A unix time of stop time
  * @return boolean TRUE if in between start and stop time
  */
  public function timeIsValid($start, $stop);

  /**
  * Returns TRUE or FALSE for X % of the time
  * @param $feature array - A associative array of the features attributes
  * @return boolean
  */
  public function parsePercentage($featureValue);

  /**
  * Returns integer value of a feature
  * @param $feature array - A associative array of the features attributes
  * @return integer
  */
  public function parseInt($featureValue);

  /**
  * Returns the string value
  * @param $feature array - An associative array of the features attributes
  * @return string
  */
  public function parseString($feature);

  /**
  * Returns a string if it is within the start and stop time bounds. Otherwise it returns false.
  * @param $feature array - A associative array of the features attributes
  * @return string/boolean
  */
  public function parseTimeString($featureValue);

  /**
  * Returns TRUE or FALSE based on the network traffic source
  * @param $feature array - An associative array of the features attributes
  * @return boolean
  */
  public function parseTrafficSource($featureValue);

  /**
   * Returns TRUE or FALSE based on a browser cookie value
   * @param $feature array - An associative array of the features attributes
   * @return boolean
   */
  public function parseCookie($featureValue);
}